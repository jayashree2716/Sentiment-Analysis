# import sys
# import matplotlib
# matplotlib.use("Agg")   # required for WAMP
# import matplotlib.pyplot as plt
# import pandas as pd
# import pymysql
# from nltk.sentiment import SentimentIntensityAnalyzer
# import nltk
# nltk.download('vader_lexicon')

# # -------------------------------------------------------
# # 1) GET PRODUCT ID FROM PHP
# # -------------------------------------------------------
# product_id = int(sys.argv[1])

# # -------------------------------------------------------
# # 2) CONNECT TO DATABASE
# # -------------------------------------------------------
# conn = pymysql.connect(
#     host="localhost",
#     user="root",
#     password="",
#     database="ecommerce",
#     charset="utf8mb4"
# )
# cur = conn.cursor()

# # -------------------------------------------------------
# # 3) FETCH PRODUCT CATEGORY (IMPORTANT!)
# # -------------------------------------------------------
# cat_query = "SELECT category FROM products WHERE id = %s"
# cur.execute(cat_query, (product_id,))
# result = cur.fetchone()

# if not result:
#     print("PRODUCT_NOT_FOUND")
#     sys.exit()

# category = result[0]

# # -------------------------------------------------------
# # 4) FETCH COMMENTS FOR THIS PRODUCT
# # -------------------------------------------------------
# query = """
# SELECT product_id, user_id, username, comment, rating
# FROM comments
# WHERE product_id = %s
# """

# df = pd.read_sql(query, conn, params=[product_id])

# if df.empty:
#     print("NO_COMMENTS")
#     sys.exit()

# # -------------------------------------------------------
# # 5) SENTIMENT ANALYSIS
# # -------------------------------------------------------
# sia = SentimentIntensityAnalyzer()

# df["compound"] = df["comment"].apply(
#     lambda x: sia.polarity_scores(x)["compound"]
# )

# df["sentiment"] = df["compound"].apply(
#     lambda c: "positive" if c > 0.05 else ("negative" if c < -0.05 else "neutral")
# )

# # Overall counts
# pos = len(df[df["sentiment"] == "positive"])
# neu = len(df[df["sentiment"] == "neutral"])
# neg = len(df[df["sentiment"] == "negative"])

# # -------------------------------------------------------
# # 6) SAVE OVERALL SENTIMENT GRAPH
# # -------------------------------------------------------
# plt.figure(figsize=(8, 5))
# plt.bar(["Positive", "Neutral", "Negative"], [pos, neu, neg])
# plt.title(f"Overall Sentiment for Product {product_id}")

# overall_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\overall_sentiment_product_{product_id}.png"
# plt.savefig(overall_path)
# plt.close()

# # -------------------------------------------------------
# # 7) FEATURE-BASED SENTIMENT (DIFFERENT FEATURES BY CATEGORY)
# # -------------------------------------------------------

# if category.lower() == "mobiles":
#     FEATURES = ["battery", "camera", "display", "sound", "performance", "charging", "processor"]
# elif category.lower() == "laptops":
#     FEATURES = ["performance", "battery", "display", "keyboard", "screen", "build", "graphics"]
# else:
#     FEATURES = ["quality", "value", "performance"]  # fallback

# # -------------------------------
# # MULTI-KEYWORD FEATURE DICTIONARY
# # -------------------------------
# FEATURE_KEYWORDS = {
#     "camera": ["camera", "cam", "photo", "picture", "pic", "photos", "video", "recording", "image", "📷", "📸", "🎥"],
#     "battery": ["battery", "backup", "charging", "charge", "drain", "mah", "🔋","heats"],
#     "display": ["display", "screen", "resolution", "amoled", "lcd", "brightness"],
#     "sound": ["sound", "speaker", "audio", "volume", "noise"],
#     "performance": ["performance", "lag", "smooth", "fast", "slow", "hang", "processor"],
# }


# feature_scores = {}

# for feature in FEATURES:

#     # Get keyword list for current feature
#     keywords = FEATURE_KEYWORDS.get(feature, [feature])

#     # Build a regex pattern for multiple keywords (OR condition)
#     pattern = "|".join([k.replace("+", "\\+") for k in keywords])

#     # Filter comments containing any keyword
#     sub_df = df[df["comment"].str.contains(pattern, case=False, na=False)]

#     if sub_df.empty:
#         feature_scores[feature] = {"pos": 0, "neu": 0, "neg": 0}
#         continue

#     pos_f = len(sub_df[sub_df["compound"] > 0.05])
#     neu_f = len(sub_df[(sub_df["compound"] >= -0.05) & (sub_df["compound"] <= 0.05)])
#     neg_f = len(sub_df[sub_df["compound"] < -0.05])

#     feature_scores[feature] = {"pos": pos_f, "neu": neu_f, "neg": neg_f}

    
#     if sub_df.empty:
#         feature_scores[feature] = {"pos": 0, "neu": 0, "neg": 0}
#         continue
    
#     pos_f = len(sub_df[sub_df["compound"] > 0.05])
#     neu_f = len(sub_df[(sub_df["compound"] >= -0.05) & (sub_df["compound"] <= 0.05)])
#     neg_f = len(sub_df[sub_df["compound"] < -0.05])
    
#     feature_scores[feature] = {"pos": pos_f, "neu": neu_f, "neg": neg_f}

# # Plot feature graph
# plt.figure(figsize=(12, 6))

# for feature, score in feature_scores.items():
#     plt.bar(feature, score["pos"], label="Positive", bottom=0)
#     # Optional: you can stack bars if needed

# plt.xticks(rotation=45)
# plt.title(f"Feature-based Sentiment for Product {product_id}")

# feature_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\feature_sentiment_product_{product_id}.png"
# plt.savefig(feature_path)
# plt.close()

# print("DONE")


import sys
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
import pandas as pd
import pymysql
import re

import nltk
from nltk.sentiment import SentimentIntensityAnalyzer
from nltk.tokenize import sent_tokenize

nltk.download('vader_lexicon')
nltk.download('punkt')

# -------------------------------------------------------
# 1) GET PRODUCT ID FROM PHP
# -------------------------------------------------------
product_id = int(sys.argv[1])

# -------------------------------------------------------
# 2) CONNECT TO DATABASE
# -------------------------------------------------------
conn = pymysql.connect(
    host="localhost",
    user="root",
    password="",
    database="ecommerce",
    charset="utf8mb4"
)
cur = conn.cursor()

# -------------------------------------------------------
# 3) FETCH PRODUCT CATEGORY
# -------------------------------------------------------
cat_query = "SELECT category FROM products WHERE id = %s"
cur.execute(cat_query, (product_id,))
result = cur.fetchone()

if not result:
    print("PRODUCT_NOT_FOUND")
    sys.exit()

category = result[0].lower()

# -------------------------------------------------------
# 4) FETCH COMMENTS FOR THIS PRODUCT
# -------------------------------------------------------
query = """
SELECT product_id, user_id, username, comment, rating
FROM comments
WHERE product_id = %s
"""

df = pd.read_sql(query, conn, params=[product_id])

if df.empty:
    print("NO_COMMENTS")
    sys.exit()

# -------------------------------------------------------
# 5) ADVANCED SENTIMENT ANALYSIS (VADER + CUSTOM BOOST)
# -------------------------------------------------------
sia = SentimentIntensityAnalyzer()

# Strong negative booster dictionary
NEGATIVE_BOOST_WORDS = [
    "worst", "bad", "terrible", "ghatiya", "pathetic", "fake", "poor",
    "drain", "heating", "heats", "hang", "lag", "slow", "waste", "useless",
    "not good", "very bad", "bakwas", "nonsense", "cheap quality",
    "overheat", "heating issue", "battery drain"
]

# Add booster words to VADER lexicon
for w in NEGATIVE_BOOST_WORDS:
    sia.lexicon[w] = -3.0  # stronger weight

def custom_sentiment(text):
    txt = text.lower()
    score = sia.polarity_scores(txt)["compound"]

    # Apply additional penalty for strong negative words
    for w in NEGATIVE_BOOST_WORDS:
        if w in txt:
            score -= 0.15

    # Adjusted thresholds (much more accurate)
    if score > 0.10:
        return "positive", score
    elif score < -0.10:
        return "negative", score
    else:
        return "neutral", score

df["sentiment"], df["compound"] = zip(*df["comment"].apply(custom_sentiment))

# Overall counts
pos = len(df[df["sentiment"] == "positive"])
neu = len(df[df["sentiment"] == "neutral"])
neg = len(df[df["sentiment"] == "negative"])

# -------------------------------------------------------
# 6) OVERALL GRAPH
# -------------------------------------------------------
plt.figure(figsize=(8, 5))
plt.bar(["Positive", "Neutral", "Negative"], [pos, neu, neg])
plt.title(f"Overall Sentiment for Product {product_id}")

overall_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\overall_sentiment_product_{product_id}.png"
plt.savefig(overall_path)
plt.close()

# -------------------------------------------------------
# 7) FEATURE-BASED SENTIMENT (ASPECT ANALYSIS)
# -------------------------------------------------------

# FEATURE LISTS
if category == "mobiles":
    FEATURES = ["battery", "camera", "display", "sound", "performance", "charging", "processor"]
elif category == "laptops":
    FEATURES = ["performance", "battery", "display", "keyboard", "screen", "build", "graphics"]
else:
    FEATURES = ["quality", "value", "performance"]

# FEATURE KEYWORDS DICTIONARY
FEATURE_KEYWORDS = {
    "camera": ["camera", "cam", "photo", "picture", "photos", "video", "record"],
    "battery": ["battery", "backup", "drain", "charge", "charging", "mah", "heat", "heats"],
    "display": ["display", "screen", "amoled", "lcd", "brightness", "resolution"],
    "sound": ["sound", "speaker", "audio", "volume"],
    "keyboard": ["keyboard", "keys", "typing"],
    "performance": ["performance", "lag", "hang", "slow", "fast", "smooth", "processor"],
    "graphics": ["graphics", "gpu", "nvidia", "amd"],
}

# Fallback: If no keyword list exists
for f in FEATURES:
    if f not in FEATURE_KEYWORDS:
        FEATURE_KEYWORDS[f] = [f]

def get_feature_sentiment(comment, keywords):
    """
    Split review into sentences and evaluate only those containing the feature keywords.
    """
    sentences = sent_tokenize(comment.lower())
    results = []

    for sent in sentences:
        if any(k in sent for k in keywords):
            label, score = custom_sentiment(sent)
            results.append(label)

    return results

feature_scores = {}

for feature in FEATURES:
    keywords = FEATURE_KEYWORDS.get(feature, [feature])
    all_sentiments = df["comment"].apply(lambda c: get_feature_sentiment(c, keywords))

    pos_f = sum(s == "positive" for lst in all_sentiments for s in lst)
    neu_f = sum(s == "neutral" for lst in all_sentiments for s in lst)
    neg_f = sum(s == "negative" for lst in all_sentiments for s in lst)

    feature_scores[feature] = {"pos": pos_f, "neu": neu_f, "neg": neg_f}

# -------------------------------------------------------
# 8) FEATURE GRAPH
# -------------------------------------------------------
plt.figure(figsize=(12, 6))

for feature, score in feature_scores.items():
    plt.bar(feature, score["pos"])

plt.xticks(rotation=45)
plt.title(f"Feature-Based Sentiment for Product {product_id}")

feature_path = f"C:\\wamp64\\www\\ecommerce\\images\\uploads\\sentiment\\feature_sentiment_product_{product_id}.png"
plt.savefig(feature_path)
plt.close()

print("DONE")

