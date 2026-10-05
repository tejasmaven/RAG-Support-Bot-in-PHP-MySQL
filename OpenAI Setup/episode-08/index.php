<!-- index.php — a tiny chat UI -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Nimbus HelpDesk AI</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 640px; margin: 40px auto; }
    #log { border: 1px solid #ddd; border-radius: 8px; padding: 16px; min-height: 240px; }
    .q { color: #1E3A8A; font-weight: 600; margin-top: 12px; }
    .a { white-space: pre-wrap; margin: 4px 0 12px; }
    form { display: flex; gap: 8px; margin-top: 12px; }
    input { flex: 1; padding: 10px; }
    button { padding: 10px 16px; }
  </style>
</head>
<body>
  <h2>Nimbus HelpDesk AI</h2>
  <div id="log"></div>
  <form id="f">
    <input id="q" placeholder="Ask about refunds, accounts, billing..." autocomplete="off" required>
    <button>Ask</button>
  </form>

  <script>
    const log = document.getElementById('log');
    document.getElementById('f').addEventListener('submit', async (e) => {
      e.preventDefault();
      const q = document.getElementById('q');
      const question = q.value.trim();
      if (!question) return;

      log.innerHTML += `<div class="q">You: ${question}</div>`;
      q.value = '';

      const res  = await fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'question=' + encodeURIComponent(question)
      });
      const data = await res.json();
      log.innerHTML += `<div class="a">${data.answer || data.error}</div>`;
      log.scrollTop = log.scrollHeight;
    });
  </script>
</body>
</html>
