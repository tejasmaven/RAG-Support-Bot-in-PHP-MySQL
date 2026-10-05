# Episode 1 — What Is RAG (first grounded call)

Companion code for: **Episode 1 — What Is RAG, and Why Should You Care?**

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **An OpenAI API key:**
  1. Sign in at <https://platform.openai.com/api-keys>.
  2. Click **Create new secret key**, name it, and copy the value (it is shown only once).
  3. Add a little credit under **Settings → Billing** — the whole series costs only cents.
  4. Put it in your `.env` as `OPENAI_API_KEY`.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and add your `OPENAI_API_KEY`.
2. `php ask.php`

Retrieval is faked here (context is pasted by hand) so you can *feel* grounding
before building the real pipeline in later episodes.
