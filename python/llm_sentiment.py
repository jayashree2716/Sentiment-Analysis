#!/usr/bin/env python3

import os
import json
import re
import pandas as pd
import pymysql

# ======================================================
#                🔑 API KEY
# ======================================================
GEMINI_API_KEY = " AIzaSyC-GF9a0RTl3CyLvHB5vXuyQqjcz4sMpQo"

from google import genai
CLIENT = genai.Client(api_key=GEMINI_API_KEY)

# ======================================================
#             MYSQL DATABASE CONFIG
# ======================================================
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",               # change if any password is set
    "database": "ecommerce",      # your DB name
    "charset": "utf8mb4"
}

# ======================================================
#          FEATURE LIST
# ======================================================
FEATURES = [
    "battery life", "battery", "battery drain",
    "camera quality", "camera",
    "display quality", "display",
    "performance", "lag", "heating"
]

NORMALIZE = {
    "battery": "battery life",
    "battery drain": "battery life",
    "camera": "camera quality",
    "display": "display quality",
}

# ======================================================
#       ZERO-SHOT PROMPT FOR AGGREGATED FEATURES
# ======================================================
SYSTEM_PROMPT = """
You are an expert aspect-based sentiment analyzer.

Given a list of reviews and a feature list:

1. Identify features being talked about.
2. Detect sentiment (positive, neutral, negative) towards each feature.
3. Aggregate sentiments across ALL reviews (NO per-review output).

Output STRICT JSON ONLY:

{
 "features": {
    "<feature>": {
       "positive": <number>,
       "neutral": <number>,
       "negative": <number>,
       "overall": "positive|neutral|negative"
    }
 }
}

Rules:
- Use ONLY the provided feature list.
- Normalize synonyms (battery → battery life, camera → camera quality, etc.)
- Determine overall as:
    > positive if positive > negative
    > negative if negative > positive
    > neutral otherwise
"""

USER_TEMPLATE = """
Features:
{features}

Reviews:
{reviews}

Return ONLY JSON.
"""

# ======================================================
#          JSON EXTRACTOR
# ======================================================
def extract_json_from_text(text):
    try:
        return json.loads(text)
    except:
        pass

    matches = re.findall(r'(\{.*?\})', text, re.DOTALL)
    for m in matches:
        try:
            return json.loads(m)
        except:
            pass

    raise ValueError("Gemini did not return valid JSON.")


# ======================================================
#          FETCH REVIEWS FROM DATABASE
# ======================================================
def fetch_reviews_from_db(product_id):
    conn = pymysql.connect(**DB_CONFIG)
    cur = conn.cursor()
    cur.execute(
        "SELECT comment FROM comments WHERE product_id=%s ORDER BY created_at ASC",
        (product_id,)
    )
    rows = cur.fetchall()
    cur.close()
    conn.close()

    reviews = [r[0] for r in rows if r[0] and r[0].strip() != ""]
    return reviews


# ======================================================
#          GEMINI ZERO-SHOT AGGREGATION
# ======================================================
def analyze_features(reviews, features):

    final_prompt = USER_TEMPLATE.format(
        features=json.dumps(features),
        reviews=json.dumps(reviews, indent=2)
    )

    response = CLIENT.models.generate_content(
        model="gemini-2.0-flash",
        contents=[SYSTEM_PROMPT + "\n\n" + final_prompt]
    )

    raw = response.text
    return extract_json_from_text(raw)


# ======================================================
#                     MAIN
# ======================================================
def main(product_id):
    print("\n📥 Fetching reviews from database...\n")

    reviews = fetch_reviews_from_db(product_id)

    if not reviews:
        print("❌ No reviews found for product:", product_id)
        return

    print(f"✔ Loaded {len(reviews)} reviews.")

    print("\n🚀 Running Feature-Based Sentiment (Aggregated)...\n")

    result = analyze_features(reviews, FEATURES)

    print(json.dumps(result, indent=2))

    # Save results
    os.makedirs("feature_overall_results", exist_ok=True)
    with open(f"feature_overall_results/summary_product_{product_id}.json", "w") as f:
        json.dump(result, f, indent=2)

    print("\n✅ DONE — Saved to feature_overall_results/summary_product_{product_id}.json")


if __name__ == "__main__":
    import sys
    try:
        pid = int(sys.argv[1])
    except:
        pid = 1
        print("\n⚠ No product_id provided — defaulting to product_id = 1\n")

    main(pid)