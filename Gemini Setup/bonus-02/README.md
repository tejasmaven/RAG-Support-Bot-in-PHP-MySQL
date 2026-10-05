# Bonus 2 — Local News RAG (Recency-Aware Answers)

Companion code for: **Bonus 2**. Adds recency-aware generation on top of Bonus 1:
a freshness blend, a time-aware prompt (today's date injected), and multi-source
citations with links + timestamps.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **A free Gemini API key:**
  1. Go to <https://aistudio.google.com/apikey> and sign in with a Google account.
  2. Click **Create API key** and copy the value.
  3. Put it in your `.env` as `GEMINI_API_KEY`. The free tier is plenty for the whole series.
- **A free GNews API key** (for the live-news data source):
  1. Register at <https://gnews.io/register> and verify your email.
  2. Copy your key from the dashboard into `.env` as `GNEWS_API_KEY` (100 requests/day on the free tier).

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in (Google Gemini + GNews keys).
2. `mysql -u root -p < schema-news.sql` and `mysql -u root -p < functions.sql`
3. `php ingest-news.php "Mumbai"`
4. `php news-bot.php "any updates on local train delays?"`
