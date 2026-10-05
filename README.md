# RAG-Powered Support Bot in PHP & MySQL

Companion code for the series
**[Build a RAG-Powered Support Bot: From Zero to Production](https://blog.tejaspmehta.com/build-a-rag-powered-support-bot-in-php-mysql/)**.

Two identical implementations — pick whichever LLM provider you prefer:

- **`Gemini Setup/`** — uses **Google Gemini**. *Recommended to start:* the free
  tier ([aistudio.google.com/apikey](https://aistudio.google.com/apikey)) lets
  you build the entire series at no cost.
- **`OpenAI Setup/`** — uses **OpenAI** (`text-embedding-3-small` + `gpt-4o-mini`).

The two trees are identical except the small provider layer — `embed.php`,
`chat.php`, and each Setup's `.env.example`. Everything else (chunking, MySQL
vector search, retrieval, prompts, guardrails, evaluation) is the same code.

## Structure (both setups)

Each folder is a **checkpoint** for one episode. The one shared exception is
`.env` / `config.php`, which live once at the Setup root (`Gemini Setup/.env`,
`OpenAI Setup/.env`) and are used by every episode and bonus folder in that
tree — set them up once per provider, not per episode:

```
<setup>/
├── episode-01/  What is RAG (first grounded call)
├── episode-02/  Loading & chunking the knowledge base
├── episode-03/  Embeddings & first similarity search
├── episode-04/  Scaling the vector search in MySQL
├── episode-05/  Retrieval: top-K, thresholds & filtering
├── episode-06/  Generation: grounded answers with citations
├── episode-07/  Hybrid search, reranking & guardrails
├── episode-08/  Evaluating & shipping (evals + web UI)
├── bonus-01/    Local News RAG — build (live data ingestion)
└── bonus-02/    Local News RAG — recency-aware answers
```

## Requirements

- **PHP 8.0+** with cURL, **MySQL 8.0+** (stock — no MySQL 9, no plugins)
- **Composer** (only for the optional PDF loader in Episode 2)
- An API key for your chosen provider:
  - **Gemini (free):** https://aistudio.google.com/apikey
  - **OpenAI:** https://platform.openai.com/api-keys
- A free **GNews** key for the bonus project: https://gnews.io/register

## Quick start

```bash
cd "Gemini Setup"
cp .env.example .env          # once, here at the Setup root — add your key(s) + MySQL credentials
cd episode-06
mysql -u root -p < schema.sql
mysql -u root -p < functions.sql
php ingest.php
php build-index.php
php ask.php "how do I get a refund?"
```

Each folder's own `README.md` has the exact steps for that episode.

> **Heads-up:** the two providers return different-sized embedding vectors
> (OpenAI 1536, Gemini 768 by default). Both setups use the same database
> (`nimbus`), so don't query a DB built with one provider using the other —
> re-run `build-index.php` if you switch setups.
>
> Never commit your `.env` — it holds your API keys. It's already gitignored.
