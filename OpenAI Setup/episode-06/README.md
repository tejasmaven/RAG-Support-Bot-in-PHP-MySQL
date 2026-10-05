# Episode 6 — Generation: Grounded Answers with Citations

Companion code for: **Episode 6**. The full RAG loop: retrieve -> grounding
prompt -> cited answer, with a safe "I don't know" when nothing is relevant.

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
4. `php ask.php "I was charged twice, can I get a refund?"`
   (try `"what's the weather?"` to see the graceful refusal)
