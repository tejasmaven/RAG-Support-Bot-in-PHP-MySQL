# Episode 2 — Preparing the Knowledge Base (loading & chunking)

Companion code for: **Episode 2 — Preparing the Knowledge Base**.

## Prerequisites

- **PHP 8+** with the cURL extension enabled (run `php -v` to check).
- **No API key is needed for this episode** — it is pure PHP text processing.
- **Composer** is optional, only if you want the PDF loader: `composer require smalot/pdfparser`.

## Run
1. (Optional, for PDF files) `composer install`
2. `php ingest.php`

Reads every file in `knowledge-base/`, cleans + chunks it, and writes
`chunks.json` — the hand-off to Episode 3.

Sample docs are Markdown so it runs with zero dependencies; drop in a `.pdf`
and run `composer install` to exercise the PDF loader too.
