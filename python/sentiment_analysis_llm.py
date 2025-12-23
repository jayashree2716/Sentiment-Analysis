# sentiment_analysis_llm.py
# LLM-based feature extraction + sentiment analysis script
# Usage: python sentiment_analysis_llm.py <product_id>

import os
import sys
import json
import time
import math
from typing import List, Dict
import pandas as pd
import pymysql
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
from tqdm import tqdm

# Optional offline fallback
import nltk
from nltk.sentiment import SentimentIntensityAnalyzer
nltk.download("vader_lexicon", quiet=True)

# -------------------------
# Config
# -------------------------
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",        # change if set
    "database": "ecommerce",
    "charset": "utf8mb4"
}

OUT_DIR = r"C:\wamp64\www\ecommerce\images\uploads\sentiment"
os.makedirs(OUT_DIR, exist_ok=True)

# LLM config - uses OpenAI python client if OPENAI_API_KEY set
# You can change MODEL to whichever model you have access to.
LLM_MODEL = os.getenv("LLM_MODEL", "gpt-4o-mini")  # change if needed
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", None)

if OPENAI_API_KEY:
    import openai
    openai.api_key = OPENAI_API_KEY

# Batch size for comments per LLM call (reduce to avoid too-large prompts)
BATCH_SIZE = 25

# Predefined features per category (extend as you like)
CATEGORY_FEATURES = {
    "mobiles": ["battery", "camera", "display", "sound", "performance", "charging", "processor", "network", "design"],
    "laptops": ["performance", "battery", "display", "keyboard", "trackpad", "graphics", "build", "heat", "ports"],
    # fallback
    "default": ["quality", "value", "performance"]
}

# -------------------------
# Utility functions
# -------------------------
def get_product_category(conn, product_id: int) -> str:
    cur = conn.cursor()
    cur.execute("SELECT category FROM products WHERE id = %s", (product_id,))
    r = cur.fetchone()
    cur.close()
    if not r:
        raise ValueError(f"Product id {product_id} not found")
    return (r[0] or "").strip()

def fetch_comments_df(conn, product_id: int) -> pd.DataFrame:
    query = """
    SELECT id, product_id, user_id, username, comment, rating, created_at
    FROM comments
    WHERE product_id = %s
    ORDER BY created_at ASC
    """
    df = pd.read_sql(query, conn, params=[product_id])
    return df

def clean_text(s: str) -> str:
    if s is None:
        return ""
    # basic cleanup — you can expand as needed
    return " ".join(str(s).strip().split())

# -------------------------
# LLM helpers
# -------------------------
def llm_analyze_batch(comments: List[Dict], features: List[str]) -> List[Dict]:
    """
    Send a batch of comments to the LLM and ask it to return for each comment:
      - features_mentioned: list of features from provided features (case-insensitive)
      - sentiment: positive/neutral/negative

    Returns list of dicts matching comments order with keys:
      id, features_mentioned (list), sentiment (str)
    """
    # If no API key, return empty -> fallback will handle
    if not OPENAI_API_KEY:
        raise RuntimeError("OPENAI_API_KEY not set — cannot call LLM")

    # Build prompt: request compact JSON array
    # For safety: instruct to ONLY output JSON.
    features_str = ", ".join(features)
    items = []
    for c in comments:
        # keep id so we can map back
        items.append({
            "id": c["id"],
            "comment": c["comment"]
        })

    system = (
        "You are a JSON-only classifier. For each comment provided, "
        "identify which of the provided features are mentioned (exact or inferred), "
        "and the sentiment about the comment (positive, neutral, negative). "
        "Output a JSON array of objects exactly (no prose) with keys: id (int), "
        "features_mentioned (array of strings), sentiment (one of 'positive','neutral','negative'). "
        "Do not output anything else."
    )

    user = (
        f"Features list: [{features_str}]\n\n"
        f"Comments (JSON array):\n{json.dumps(items, ensure_ascii=False)}\n\n"
        "For feature detection, be permissive: a mention of 'camera is great' -> feature 'camera'. "
        "If a comment mentions multiple features, list all that apply. "
        "Sentiment rules: positive if overall positive, negative if overall negative, otherwise neutral."
    )

    # Use chat completion
    # We'll retry a few times on transient errors.
    max_retries = 3
    for attempt in range(max_retries):
        try:
            resp = openai.ChatCompletion.create(
                model=LLM_MODEL,
                messages=[
                    {"role": "system", "content": system},
                    {"role": "user", "content": user}
                ],
                temperature=0.0,
                max_tokens=1500
            )
            text = resp["choices"][0]["message"]["content"].strip()
            # parse JSON
            parsed = json.loads(text)
            # ensure parsed is list and contains id/sentiment/features
            results = []
            for obj in parsed:
                results.append({
                    "id": int(obj["id"]),
                    "features_mentioned": obj.get("features_mentioned") or [],
                    "sentiment": obj.get("sentiment") or "neutral"
                })
            return results
        except Exception as e:
            print(f"LLM call attempt {attempt+1} failed: {e}")
            time.sleep(1 + attempt*2)
            last_exc = e
    raise last_exc

# -------------------------
# Fallback offline sentiment (VADER)
# -------------------------
sia = SentimentIntensityAnalyzer()
def vader_sentiment(text: str) -> str:
    if not text or str(text).strip()=="":
        return "neutral"
    s = sia.polarity_scores(text)
    c = s["compound"]
    if c > 0.05:
        return "positive"
    if c < -0.05:
        return "negative"
    return "neutral"

# -------------------------
# Main pipeline
# -------------------------
def main(product_id: int):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        category = get_product_category(conn, product_id)
        print(f"Product {product_id} category: '{category}'")
        df = fetch_comments_df(conn, product_id)
        if df.empty:
            print("No comments for product", product_id)
            return

        # Clean text
        df["comment_clean"] = df["comment"].astype(str).apply(clean_text)
        comments_list = df.to_dict("records")

        # choose features
        cat_key = category.strip().lower()
        features = CATEGORY_FEATURES.get(cat_key, CATEGORY_FEATURES.get("default"))
        print("Using features:", features)

        # If OPENAI configured, use LLM for feature extraction + sentiment classification
        results = []
        if OPENAI_API_KEY:
            # batch through comments
            for i in range(0, len(comments_list), BATCH_SIZE):
                batch = comments_list[i:i+BATCH_SIZE]
                # prepare simplified batch dicts
                input_batch = [{"id": c["id"], "comment": c["comment_clean"]} for c in batch]
                try:
                    out = llm_analyze_batch(input_batch, features)
                except Exception as e:
                    print("LLM failed for batch — falling back to VADER for sentiment and keyword match for features.", e)
                    # fallback: keyword matching for features + VADER sentiment
                    out = []
                    for c in input_batch:
                        matched = [f for f in features if f.lower() in (c["comment"] or "").lower()]
                        out.append({"id": c["id"], "features_mentioned": matched, "sentiment": vader_sentiment(c["comment"])})
                results.extend(out)
        else:
            # Full offline: simple keyword feature detection + VADER
            for c in comments_list:
                matched = [f for f in features if f.lower() in (c["comment_clean"] or "").lower()]
                results.append({"id": c["id"], "features_mentioned": matched, "sentiment": vader_sentiment(c["comment_clean"])})

        # Map results back into dataframe
        res_df = pd.DataFrame(results)
        merged = df.merge(res_df, left_on="id", right_on="id", how="left")
        merged["sentiment"] = merged["sentiment"].fillna("neutral")
        merged["features_mentioned"] = merged["features_mentioned"].apply(lambda x: x if isinstance(x, list) else [])

        # Overall sentiment counts
        overall_counts = merged["sentiment"].value_counts().to_dict()
        pos = overall_counts.get("positive", 0)
        neu = overall_counts.get("neutral", 0)
        neg = overall_counts.get("negative", 0)

        # Per-feature counts (count of comments mentioning each feature and sentiment breakdown)
        feature_summary = {}
        for f in features:
            sub = merged[merged["features_mentioned"].apply(lambda arr: f in arr)]
            feature_summary[f] = {
                "count": len(sub),
                "positive": int((sub["sentiment"] == "positive").sum()),
                "neutral": int((sub["sentiment"] == "neutral").sum()),
                "negative": int((sub["sentiment"] == "negative").sum())
            }

        # Save overall plot
        plt.figure(figsize=(8,5))
        labels = ["Positive","Neutral","Negative"]
        values = [pos, neu, neg]
        plt.bar(labels, values)
        plt.title(f"Overall Sentiment for Product {product_id}")
        overall_path = os.path.join(OUT_DIR, f"overall_sentiment_product_{product_id}.png")
        plt.savefig(overall_path)
        plt.close()
        print("Saved overall sentiment to", overall_path)

        # Save feature plot (stacked bars: pos/neu/neg)
        features_order = list(feature_summary.keys())
        pos_vals = [feature_summary[f]["positive"] for f in features_order]
        neu_vals = [feature_summary[f]["neutral"] for f in features_order]
        neg_vals = [feature_summary[f]["negative"] for f in features_order]

        x = range(len(features_order))
        plt.figure(figsize=(max(10, len(features_order)*1.2), 6))
        plt.bar(x, pos_vals, label="positive")
        plt.bar(x, neu_vals, bottom=pos_vals, label="neutral")
        bottom_neg = [pos_vals[i] + neu_vals[i] for i in range(len(pos_vals))]
        plt.bar(x, neg_vals, bottom=bottom_neg, label="negative")
        plt.xticks(x, features_order, rotation=45, ha='right')
        plt.title(f"Feature-wise Sentiment for Product {product_id}")
        plt.legend()
        feature_path = os.path.join(OUT_DIR, f"feature_sentiment_product_{product_id}.png")
        plt.tight_layout()
        plt.savefig(feature_path)
        plt.close()
        print("Saved feature sentiment to", feature_path)

        # Optionally write a small JSON summary
        summary = {"product_id": product_id, "category": category, "overall": {"positive": pos, "neutral": neu, "negative": neg}, "features": feature_summary}
        with open(os.path.join(OUT_DIR, f"summary_product_{product_id}.json"), "w", encoding="utf-8") as fh:
            json.dump(summary, fh, ensure_ascii=False, indent=2)
        print("Saved summary json.")

    finally:
        conn.close()

if __name__ == "__main__":
    # Accept product_id from argv or default for Notebook
    try:
        pid = int(sys.argv[1])
    except Exception:
        print("No product_id provided - defaulting to 1 for local testing.")
        pid = 1
    main(pid)
