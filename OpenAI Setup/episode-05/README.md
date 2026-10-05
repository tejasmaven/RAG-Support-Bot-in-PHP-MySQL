# Episode 5 — Retrieval: Top-K, Thresholds & Filtering

Companion code for: **Episode 5**. Adds a real `retrieve()` function with top-K,
a quality threshold, per-source diversity, and a context budget.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **An OpenAI API key:**
  1. Sign in at <https://platform.openai.com/api-keys>.
  2. Click **Create new secret key**, name it, and copy the value (it is shown only once).
  3. Add a little credit under **Settings → Billing** — the whole series costs only cents.
  4. Put it in your `.env` as `OPENAI_API_KEY`.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in.
2. `mysql -u root -p < schema.sql` and `mysql -u root -p < functions.sql`
3. `php ingest.php`  then  `php build-index.php`
4. Calibrate a threshold on your data:  `php calibrate.php`
5. `php test-retrieve.php "how do I get a refund?"`
   (try `"what's the weather today?"` to see an empty result)
