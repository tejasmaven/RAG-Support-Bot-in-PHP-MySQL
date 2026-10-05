# Bonus 2 — Local News RAG (Recency-Aware Answers)

Companion code for: **Bonus 2**. Adds recency-aware generation on top of Bonus 1:
a freshness blend, a time-aware prompt (today's date injected), and multi-source
citations with links + timestamps.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **An OpenAI API key:**
  1. Sign in at <https://platform.openai.com/api-keys>.
  2. Click **Create new secret key**, name it, and copy the value (it is shown only once).
  3. Add a little credit under **Settings → Billing** — the whole series costs only cents.
  4. Put it in your `.env` as `OPENAI_API_KEY`.
- **A free GNews API key** (for the live-news data source):
  1. Register at <https://gnews.io/register> and verify your email.
  2. Copy your key from the dashboard into `.env` as `GNEWS_API_KEY` (100 requests/day on the free tier).

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in (OpenAI + GNews keys).
2. `mysql -u root -p < schema-news.sql` and `mysql -u root -p < functions.sql`
3. `php ingest-news.php "Mumbai"`
4. `php news-bot.php "any updates on local train delays?"`
