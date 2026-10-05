# Episode 3 — Embeddings & Your First Similarity Search

Companion code for: **Episode 3**. Cosine similarity is computed in PHP so you
can see exactly how retrieval works.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **An OpenAI API key:**
  1. Sign in at <https://platform.openai.com/api-keys>.
  2. Click **Create new secret key**, name it, and copy the value (it is shown only once).
  3. Add a little credit under **Settings → Billing** — the whole series costs only cents.
  4. Put it in your `.env` as `OPENAI_API_KEY`.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill in your OpenAI key + MySQL credentials.
2. Create the table:  `mysql -u root -p < schema.sql`
3. Build `chunks.json`:  `php ingest.php`   (from Episode 2, included here)
4. Embed + store:  `php build-index.php`
5. Search:  `php search.php "how do I get a refund?"`
