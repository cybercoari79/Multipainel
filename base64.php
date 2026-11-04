<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerador de Links Base64</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: linear-gradient(135deg, #1e1e2e, #2a2a40);
    color: #e0e0e0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }
  .container {
    background: #2d2d44;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    width: 100%;
    max-width: 600px;
  }
  h1 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    text-align: center;
    color: #7dd3fc;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #a0a0b8;
  }
  textarea, input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #444;
    background: #1e1e2e;
    color: #e0e0e0;
    font-family: inherit;
    font-size: 0.95rem;
    resize: vertical;
    outline: none;
    transition: border 0.2s;
  }
  textarea:focus, input:focus {
    border-color: #7dd3fc;
  }
  textarea { min-height: 90px; }
  .botoes {
    display: flex;
    gap: 10px;
    margin: 15px 0;
  }
  button {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.1s, opacity 0.2s;
  }
  button:hover { opacity: 0.9; }
  button:active { transform: scale(0.98); }
  .btn-gerar { background: #7dd3fc; color: #1e1e2e; }
  .btn-limpar { background: #444; color: #e0e0e0; }
  .btn-copiar { background: #34d399; color: #1e1e2e; }
  .resultado {
    background: #1e1e2e;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #444;
    word-break: break-all;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    min-height: 60px;
    color: #7dd3fc;
    margin-top: 10px;
  }
  .status {
    text-align: center;
    margin-top: 10px;
    font-size: 0.85rem;
    color: #34d399;
    min-height: 18px;
  }
  .aviso {
    margin-top: 20px;
    padding: 10px;
    background: #3a2e1e;
    border-left: 3px solid #fbbf24;
    border-radius: 6px;
    font-size: 0.8rem;
    color: #d0c0a0;
  }
</style>
</head>
<body>
<div class="container">
  <h1>🔗 Gerador de Links Base64</h1>

  <label for="entrada">URL ou Texto:</label>
  <textarea id="entrada" placeholder="Digite ou cole a URL/texto aqui..."></textarea>

  <div class="botoes">
    <button class="btn-gerar" onclick="gerar()">Codificar (→ Base64)</button>
    <button class="btn-gerar" onclick="decodificar()">Decodificar (Base64 →)</button>
  </div>

  <label>Resultado:</label>
  <div class="resultado" id="resultado">Aguardando entrada...</div>

  <div class="botoes">
    <button class="btn-copiar" onclick="copiar()">📋 Copiar</button>
    <button class="btn-limpar" onclick="limpar()">🗑️ Limpar</button>
  </div>

  <div class="status" id="status"></div>

  <div class="aviso">
    ⚠️ <strong>Atenção:</strong> Base64 não é criptografia, é apenas codificação. 
    Use apenas para fins legítimos (APIs, testes, transporte de dados).
  </div>
</div>

<script>
  const entrada = document.getElementById('entrada');
  const resultado = document.getElementById('resultado');
  const status = document.getElementById('status');

  // Codifica texto para Base64 com suporte a UTF-8
  function paraBase64(str) {
    return btoa(unescape(encodeURIComponent(str)));
  }

  // Decodifica Base64 para texto UTF-8
  function deBase64(b64) {
    return decodeURIComponent(escape(atob(b64)));
  }

  function mostrarStatus(msg, cor = '#34d399') {
    status.textContent = msg;
    status.style.color = cor;
    setTimeout(() => status.textContent = '', 3000);
  }

  function gerar() {
    const valor = entrada.value.trim();
    if (!valor) {
      resultado.textContent = '⚠️ Digite algo primeiro!';
      return;
    }
    try {
      resultado.textContent = paraBase64(valor);
      mostrarStatus('✅ Codificado com sucesso!');
    } catch (e) {
      resultado.textContent = '❌ Erro ao codificar: ' + e.message;
      mostrarStatus('Erro!', '#ef4444');
    }
  }

  function decodificar() {
    const valor = entrada.value.trim();
    if (!valor) {
      resultado.textContent = '⚠️ Digite algo primeiro!';
      return;
    }
    try {
      resultado.textContent = deBase64(valor);
      mostrarStatus('✅ Decodificado com sucesso!');
    } catch (e) {
      resultado.textContent = '❌ Base64 inválido!';
      mostrarStatus('Erro ao decodificar', '#ef4444');
    }
  }

  async function copiar() {
    const texto = resultado.textContent;
    if (!texto || texto.includes('Aguardando')) return;
    try {
      await navigator.clipboard.writeText(texto);
      mostrarStatus('📋 Copiado para a área de transferência!');
    } catch {
      // Fallback para navegadores antigos
      const ta = document.createElement('textarea');
      ta.value = texto;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      mostrarStatus('📋 Copiado!');
    }
  }

  function limpar() {
    entrada.value = '';
    resultado.textContent = 'Aguardando entrada...';
    status.textContent = '';
    entrada.focus();
  }

  // Atalhos: Ctrl+Enter codifica, Ctrl+Shift+Enter decodifica
  entrada.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'Enter') {
      e.preventDefault();
      e.shiftKey ? decodificar() : gerar();
    }
  });
</script>
</body>
</html>