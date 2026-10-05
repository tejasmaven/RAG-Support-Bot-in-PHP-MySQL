# Episode 1 — What Is RAG (first grounded call)

Companion code for: **Episode 1 — What Is RAG, and Why Should You Care?**

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **A free Gemini API key:**
  1. Go to <https://aistudio.google.com/apikey> and sign in with a Google account.
  2. Click **Create API key** and copy the value.
  3. Put it in your `.env` as `GEMINI_API_KEY`. The free tier is plenty for the whole series.

## Run
1. `cp ../.env.example ../.env` (once, at the Setup root — shared by every episode) and add your `GEMINI_API_KEY`.
2. `php ask.php`

Retrieval is faked here (context is pasted by hand) so you can *feel* grounding
before building the real pipeline in later episodes.
