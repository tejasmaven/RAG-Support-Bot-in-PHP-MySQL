# Bonus 1 — Local News RAG (Build)

Companion code for: **Bonus 1**. The same RAG skeleton pointed at live news from
an open API (GNews), with dedup and freshness handling.

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
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode); add your OpenAI key and a free GNews key
   (https://gnews.io/register).
2. `mysql -u root -p < schema-news.sql`
3. `mysql -u root -p < functions.sql`   (the dot_product function)
4. `php ingest-news.php "Mumbai"`  (run twice to see dedup skip everything)
5. `php test-news.php "what's happening with local trains?"`
