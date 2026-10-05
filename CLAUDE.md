# CLAUDE.md

Guidance for AI coding agents (Claude Code) working in this repo.

## What this is

Companion code for the blog series **"Build a RAG-Powered Support Bot: From Zero
to Production"** (https://blog.tejaspmehta.com/build-a-rag-powered-support-bot-in-php-mysql/).
It implements Retrieval-Augmented Generation by hand in **plain PHP 8 + MySQL 8**
— no frameworks, no vector database, no ORM. The demo bot is "Nimbus HelpDesk AI".

There are **two complete, mirrored implementations**, one per LLM provider:

```
RAG-Support-Bot-in-PHP-MySQL/
├── README.md
├── CLAUDE.md              <- this file
├── OpenAI Setup/          <- uses OpenAI (text-embedding-3-small + gpt-4o-mini)
│   ├── episode-01 … episode-08
│   └── bonus-01, bonus-02
└── Gemini Setup/          <- uses Google Gemini (gemini-embedding-001 + gemini-3.8-flash)
    ├── episode-01 … episode-08
    └── bonus-01, bonus-02
```

Each `OpenAI Setup/` and `Gemini Setup/` root also holds a single shared
`.env` / `.env.example` / `config.php` — see rule 2's exception below before
assuming every episode still duplicates these three files.

Folder names contain a space ("OpenAI Setup", "Gemini Setup") — quote paths.

## Two critical structural rules

1. **The two setups are line-for-line identical EXCEPT the provider layer.**
   The only files that differ between `OpenAI Setup/` and `Gemini Setup/` are:
   - `embed.php`      (embeddings API)
   - `chat.php`       (generation API)
   - `episode-01/ask.php` (Episode 1 makes the LLM call inline, no `chat.php`)
   - `.env.example`   (key names)
   - `README.md`      (provider wording)
   Everything else (chunking, MySQL vector search, retrieval, prompts, guardrails,
   evaluation, the whole news pipeline) is byte-identical.
   **When you change provider behavior, mirror the change in BOTH setups.**

2. **Each episode folder is a STANDALONE checkpoint** — it duplicates the shared
   files it needs so it can run on its own (that's intentional, it matches the
   blog's "browse any stage" goal). So `db.php`, `embed.php`, `vector.php`,
   `chunker.php`, `functions.sql`, etc. exist as **copies** in many folders.
   **A fix to a shared file must be propagated to every folder that has a
   copy, in BOTH setups.** Don't fix one copy and assume you're done.
   Quick propagation pattern:
   ```bash
   # after editing one canonical copy:
   find "OpenAI Setup" "Gemini Setup" -name db.php -exec cp /path/to/fixed/db.php {} \;
   ```

   **Exception — `.env` / `.env.example` / `config.php` are NOT duplicated.**
   They live once at each Setup's root (`Gemini Setup/.env`, `Gemini Setup/config.php`,
   etc.) and every episode/bonus file requires them one level up:
   `require_once __DIR__ . '/../config.php';`. This deliberately breaks the
   "browse any single episode folder standalone" promise for just these three
   files — readers now set up `.env` once per provider, not per episode — because
   duplicating live API keys/DB creds across 9+ folders per provider was worse
   than the minor loss of per-folder portability. If you ever add a new
   episode/bonus folder, give it `require_once __DIR__ . '/../config.php';`
   (not a local `config.php` copy) and don't create a local `.env.example`.

## Architecture (the RAG pipeline)

Offline (ingest):  `knowledge-base/*` --loader.php--> clean text --chunker.php-->
chunks --embed.php--> vectors --build-index.php--> MySQL `chunks` table.

Online (query):    question --embed.php--> vector --retrieve/hybrid--> top chunks
--context.php + prompt.php--> prompt --chat.php--> grounded, cited answer.

### File roles (support bot)
- `config.php`  — tiny `.env` loader exposing `env($key, $default)`. Strips
  surrounding quotes from values. Lives once at the Setup root (not per
  episode); episode/bonus files `require_once __DIR__ . '/../config.php';`.
- `db.php`      — single PDO MySQL connection via `db()`. From Episode 4 on it
  sets `PDO::ATTR_EMULATE_PREPARES => false` so `LIMIT :k` binds as an int.
- `embed.php`   — **provider-specific**. `getEmbedding(string): array`.
- `chat.php`    — **provider-specific**. `chat(array $messages, float $temp): string`.
  Uses OpenAI-style `$messages` ([{role, content}]) as the common interface so
  callers are identical across providers.
- `vector.php`  — `normalize(array): array` (scales a vector to length 1).
- `loader.php` / `chunker.php` / `ingest.php` — build `chunks.json` from docs.
- `build-index.php` — embed chunks and store in MySQL.
- `retrieve.php` (Ep5) — `retrieve()` with top-K, threshold, per-source diversity,
  context budget. Returns `[]` when nothing is relevant → the bot says "I don't know".
- `hybrid.php` (Ep7+) — vector + FULLTEXT keyword candidates fused with Reciprocal
  Rank Fusion; `retrieveHybrid()`.
- `context.php` — numbers/labels retrieved chunks for citation.
- `prompt.php`  — the grounding system prompt (Ep7+ is the hardened version that
  treats context as untrusted data).
- `ask.php` / `bot.php` — the full loop. Ep8 moves logic into `bot.php` (no output)
  and adds `cli.php`, `evaluate.php`, `api.php`, `index.php` (web UI).

### File roles (bonus — Local News RAG)
- `news-source.php` — fetch GNews + `normalizeArticle()` adapter (swap providers here).
- `ingest-news.php` — dedup-safe ingestion (`INSERT IGNORE` + unique URL key).
- `retrieve-news.php` — hybrid retrieval over `news_chunks`.
- `rrf.php` — Reciprocal Rank Fusion (extracted for reuse).
- `recency.php` — `applyRecency()` blends relevance with freshness.
- `news-context.php` / `news-prompt.php` / `news-bot.php` — time-aware generation.

### SQL
- `schema.sql`      — `chunks` table (embedding as JSON). Ep7+ adds a FULLTEXT index.
- `functions.sql`   — the `dot_product(a JSON, b JSON)` stored function built on
  `JSON_TABLE`. Runs on stock MySQL 8. May need
  `SET GLOBAL log_bin_trust_function_creators = 1;` before creating it.
- `schema-news.sql` — `news_chunks` table (bonus).

## Environment / keys

Each Setup root (`OpenAI Setup/`, `Gemini Setup/`) has ONE `.env.example`;
copy it to `.env` in that same root folder (gitignored — never commit). Every
episode and bonus folder under that Setup reads the same `.env` via
`config.php` one level up — do not create a per-episode `.env`.

- **OpenAI Setup:** `OPENAI_API_KEY`, `OPENAI_EMBED_MODEL` (text-embedding-3-small),
  `OPENAI_CHAT_MODEL` (gpt-4o-mini), plus `DB_*`. Key: platform.openai.com/api-keys
- **Gemini Setup:** `GEMINI_API_KEY`, `GEMINI_EMBED_MODEL` (gemini-embedding-001),
  `GEMINI_EMBED_DIM` (768), `GEMINI_CHAT_MODEL` (gemini-3.8-flash — Google
  retired 2.5-flash for new users; see gotcha below), plus `DB_*`.
  Free key: aistudio.google.com/apikey
- Bonus folders also need `GNEWS_API_KEY` (free: gnews.io/register), same
  shared `.env`.

## Run (any episode)

```bash
cd "Gemini Setup"
cp .env.example .env            # once, here at the Setup root — add keys + MySQL creds
cd episode-06
mysql -u root -p < schema.sql
mysql -u root -p < functions.sql   # episodes 4+ and bonus
php ingest.php
php build-index.php
php ask.php "how do I get a refund?"
```
Episode 8 has a web UI (`index.php` + `api.php`); with WAMP it's at
`http://localhost/RAG-Support-Bot-in-PHP-MySQL/<Setup>/episode-08/`.

## Known gotchas / decisions (read before "fixing")

- **MySQL 8 only, on purpose.** Do NOT switch to MySQL 9's `VECTOR` type — its
  `DISTANCE()` is HeatWave-only. The `JSON_TABLE` + `dot_product` approach is the
  intended solution and works on stock MySQL 8.
- **Gemini "thinking" models return empty text.** `gemini-2.5-flash`/3.x reason
  internally and burn the output budget, yielding a candidate with no text. The
  fix (already applied) is `generationConfig.thinkingConfig.thinkingBudget = 0`
  for 2.5/3.x models in Gemini `chat.php` and `episode-01/ask.php`. Keep it.
- **Gemini model names go stale.** Google retires chat models for new users
  with a 404 ("no longer available to new users... use models/X"). Default is
  now `gemini-3.8-flash` (was `gemini-2.5-flash`, retired). If you hit this
  404 again, the fix is the model name in `Gemini Setup/.env` (or `.env.example`'s
  `GEMINI_CHAT_MODEL` default) plus the `'gemini-…'` fallback in every
  `Gemini Setup/*/chat.php` and `episode-01/ask.php` — not a code/logic bug.
  The `thinkingBudget = 0` regex above (`/2\.5|3\./`) already matches 3.x
  names, so no change needed there when the model version bumps within 3.x.
- **Embedding dimensions differ:** OpenAI 1536 vs Gemini 768. Both setups use the
  same DB name `nimbus`, so a DB built with one provider is invalid for the other
  — re-run `build-index.php` after switching (or use a different `DB_NAME`).
- **PDO emulation off** (Ep4+) is required for `LIMIT :k` integer binding.
- **`chat.php` contract:** keep the `chat(array $messages, float $temperature)`
  signature identical across providers — many files depend on it.
- **Provider error surfacing:** the Gemini layer prints/throws the raw API
  response when there's no text, so failures aren't silent. Preserve that.

## Verifying changes

- No automated tests. Lint with `php -l <file>` (loop over `find . -name '*.php'`).
- Smoke test without a DB: `episode-01/ask.php` (needs only the provider key).
- Full pipeline test: any of episodes 3–8 (needs MySQL + `build-index.php`).
- After any shared-file or provider-file edit, re-check the mirror (both setups,
  all folder copies).
