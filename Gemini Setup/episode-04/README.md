# Episode 4 — Scaling the Vector Search in MySQL

Companion code for: **Episode 4**. The similarity math moves into MySQL via a
`dot_product()` stored function built on `JSON_TABLE`. Runs on stock MySQL 8.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **A free Gemini API key:**
  1. Go to <https://aistudio.google.com/apikey> and sign in with a Google account.
  2. Click **Create API key** and copy the value.
  3. Put it in your `.env` as `GEMINI_API_KEY`. The free tier is plenty for the whole series.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in.
2. `mysql -u root -p < schema.sql`
3. `mysql -u root -p < functions.sql`
4. `php ingest.php`  then  `php build-index.php`
5. `php search.php "how do I get a refund?"`
