import sys
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
import pandas as pd
import pymysql
import re
import pickle
from sklearn.linear_model import LogisticRegression
from sklearn.feature_extraction.text import TfidfVectorizer
from nltk.sentiment import SentimentIntensityAnalyzer
import nltk
nltk.download("vader_lexicon")

# ---------------------------------------------
# GET PRODUCT ID
# ---------------------------------------------
product_id = int(sys.argv[1])

# ---------------------------------------------
# CLEAN TEXT FUNCTION
# ---------------------------------------------
def clean_text(text):
    if text is None:
        return ""
    text = text.lower()
    text = re.sub(r"http\S+", "", text)
    text = re.sub(r"[^a-zA-Z\s]", " ", text)
    text = re.sub(r"\s+", " ", text)
    return text.strip()

# ---------------------------------------------
# CONNECT TO DATABASE
# ---------------------------------------------
conn = pymysql.connect(
    host="localhost",
    user="root",
    password="",
    database="ecommerce",
    charset="utf8mb4"
)
cur = conn.cursor()

# ---------------------------------------------
# FETCH PRODUCT CATEGORY
# ---------------------------------------------
cur.execute("SELECT category FROM products WHERE id=%s", (product_id,))
result = cur.fetchone()

if not result:
    print("PRODUCT_NOT_FOUND")
    sys.exit()

category = result[0].lower()

# ---------------------------------------------
# FETCH COMMENTS OF PRODUCT
# ---------------------------------------------
query = """
SELECT comment FROM comments WHERE product_id=%s
"""
df = pd.read_sql(query, conn, params=[product_id])

if df.empty:
    print("NO_COMMENTS")
    sys.exit()

df["clean"] = df["comment"].apply(clean_text)

# --------------------------------------------------
# AUTO LABEL SENTIMENT USING VADER (TRAINING DATA)
# --------------------------------------------------
sia = SentimentIntensityAnalyzer()

def vader_label(text):
    score = sia.polarity_scores(text)["compound"]
    if score > 0.05:
        return "positive"
    elif score < -0.05:
        return "negative"
    else:
        return "neutral"

df["label"] = df["clean"].apply(vader_label)

# --------------------------------------------------
# TRAIN LOGISTIC REGRESSION (ON THIS PRODUCT DATA)
# --------------------------------------------------
vectorizer = TfidfVectorizer(max_features=3000)
X = vectorizer.fit_transform(df["clean"])
y = df["label"]

model = LogisticRegression(max_iter=300)
model.fit(X, y)

# --------------------------------------------------
# PREDICT SENTIMENT USING ML MODEL
# --------------------------------------------------
df["predicted"] = model.predict(X)

# --------------------------------------------------
# OVERALL SENTIMENT COUNT
# --------------------------------------------------
pos = len(df[df["predicted"] == "positive"])
neu = len(df[df["predicted"] == "neutral"])
neg = len(df[df["predicted"] == "negative"])

# --------------------------------------------------
# SAVE OVERALL SENTIMENT GRAPH
# --------------------------------------------------
plt.figure(figsize=(8,5))
plt.bar(["Positive", "Neutral", "Negative"], [pos, neu, neg])
plt.title(f"Overall Sentiment (Logistic Regression) - Product {product_id}")

overall_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\overall_sentiment_product_{product_id}.png"
plt.savefig(overall_path)
plt.close()

# --------------------------------------------------
# DEFINE FEATURES BASED ON CATEGORY
# --------------------------------------------------
if category == "mobiles":
    FEATURES = ["battery", "camera", "display", "sound", "charging", "processor", "performance"]
elif category == "laptops":
    FEATURES = ["performance", "battery", "display", "keyboard", "screen", "graphics", "build"]
else:
    FEATURES = ["quality", "value", "performance"]

# --------------------------------------------------
# FEATURE SENTIMENT ANALYSIS
# --------------------------------------------------
feature_scores = {}

for feature in FEATURES:
    sub_df = df[df["clean"].str.contains(feature, case=False, na=False)]

    if sub_df.empty:
        feature_scores[feature] = {"pos": 0, "neu": 0, "neg": 0}
        continue

    feature_scores[feature] = {
        "pos": len(sub_df[sub_df["predicted"] == "positive"]),
        "neu": len(sub_df[sub_df["predicted"] == "neutral"]),
        "neg": len(sub_df[sub_df["predicted"] == "negative"])
    }

# --------------------------------------------------
# DRAW FEATURE GRAPH
# --------------------------------------------------
plt.figure(figsize=(12,6))

pos_vals = [feature_scores[f]["pos"] for f in FEATURES]
neu_vals = [feature_scores[f]["neu"] for f in FEATURES]
neg_vals = [feature_scores[f]["neg"] for f in FEATURES]

x = range(len(FEATURES))

plt.bar(x, pos_vals, label="Positive")
plt.bar(x, neu_vals, bottom=pos_vals, label="Neutral")
plt.bar(x, neg_vals, bottom=[pos_vals[i] + neu_vals[i] for i in x], label="Negative")

plt.xticks(x, FEATURES, rotation=45)
plt.legend()
plt.title(f"Feature Based Sentiment (Logistic Regression) - Product {product_id}")

feature_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\feature_sentiment_product_{product_id}.png"
plt.savefig(feature_path)
plt.close()

print("DONE")
