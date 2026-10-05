# Episode 7 — Improving Quality: Hybrid Search, Reranking & Guardrails

Companion code for: **Episode 7**. Adds keyword (MySQL FULLTEXT) + vector hybrid
search fused with Reciprocal Rank Fusion, an optional LLM reranker, and a
hardened prompt that treats retrieved context as untrusted data.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **MySQL 8+** running locally (stock MySQL 8 — no MySQL 9 or plugins needed).
- **A free Gemini API key:**
  1. Go to <https://aistudio.google.com/apikey> and sign in with a Google account.
  2. Click **Create API key** and copy the value.
  3. Put it in your `.env` as `GEMINI_API_KEY`. The free tier is plenty for the whole series.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and fill it in.
2. `mysql -u root -p < schema.sql`  (note the FULLTEXT index)
3. `mysql -u root -p < functions.sql`
4. `php ingest.php`  then  `php build-index.php`
5. `php ask.php "I'm getting error ERR_402 at checkout"`
