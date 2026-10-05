# Episode 8 — Evaluating & Shipping

Companion code for: **Episode 8**. Logic lives in `bot.php`; a CLI, an evaluator,
a JSON API, and a minimal chat UI all share it.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **A free Gemini API key:**
  1. Go to <https://aistudio.google.com/apikey> and sign in with a Google account.
  2. Click **Create API key** and copy the value.
  3. Put it in your `.env` as `GEMINI_API_KEY`. The free tier is plenty for the whole series.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in.
2. `mysql -u root -p < schema.sql` and `mysql -u root -p < functions.sql`
3. `php ingest.php`  then  `php build-index.php`
4. CLI:      `php cli.php "how do I get a refund?"`
5. Evaluate: `php evaluate.php`
6. Web UI:   `php -S localhost:8000` then open http://localhost:8000/
