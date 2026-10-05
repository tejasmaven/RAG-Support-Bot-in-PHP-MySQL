# Nimbus HelpDesk AI — RAG in PHP & MySQL

Companion code for the blog series
**[Build a RAG-Powered Support Bot: From Zero to Production](https://blog.tejaspmehta.com/build-a-rag-powered-support-bot-in-php-mysql/)**.

Retrieval-Augmented Generation built by hand in plain **PHP 8 + MySQL 8** — no
frameworks, no vector database, no vendor lock-in.

## Layout

Each folder is a **checkpoint** for one episode: it contains everything needed
to run that stage on its own, so you can `cd` into any folder and follow along
without the earlier ones. The one exception is `.env` / `config.php`, which
live once at this Setup's root and are shared by every episode and bonus
folder — set them up once, not per episode.

| Folder | Episode |
|---|---|
| `episode-01/` | What Is RAG (first grounded call) |
| `episode-02/` | Preparing the knowledge base (loading & chunking) |
| `episode-03/` | Embeddings & your first similarity search |
| `episode-04/` | Scaling the vector search in MySQL |
| `episode-05/` | Retrieval: top-K, thresholds & filtering |
| `episode-06/` | Generation: grounded answers with citations |
| `episode-07/` | Hybrid search, reranking & guardrails |
| `episode-08/` | Evaluating & shipping (evals + web UI) |
| `bonus-01/`   | Local News RAG — build (live data ingestion) |
| `bonus-02/`   | Local News RAG — recency-aware answers |

## Prerequisites

- **PHP 8.0+** with cURL
- **MySQL 8.0+** (stock — no MySQL 9, no plugins, no cloud vector service)
- **Composer** (only for the optional PDF loader in Episode 2)
- An **Gemini API key** (embeddings + generation) — free tier at https://aistudio.google.com/apikey
- A free **GNews API key** for the bonus (https://gnews.io/register)

## Quick start (any episode)

```bash
cp .env.example .env          # once, here at the Setup root — add your keys + DB credentials
cd episode-06
mysql -u root -p < schema.sql
mysql -u root -p < functions.sql   # episodes 4+ (the dot_product function)
php ingest.php                # build chunks.json from knowledge-base/
php build-index.php           # embed + store in MySQL
php ask.php "how do I get a refund?"
```

Each folder's own `README.md` has the exact steps for that episode.

> Never commit your `.env` — it holds your API keys. It is already gitignored.
