<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Link Base64</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 32px;
            width: 100%;
            max-width: 600px;
        }
        h1 {
            color: #333;
            margin-bottom: 8px;
            font-size: 24px;
        }
        p.subtitle {
            color: #777;
            margin-bottom: 24px;
            font-size: 14px;
        }
        label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input, textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .field { margin-bottom: 16px; }
        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        button {
            flex: 1;
            min-width: 120px;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s, opacity 0.2s;
        }
        button:active { transform: scale(0.97); }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .btn-secondary:hover { background: #e4e4e4; }
        .btn-primary:hover { opacity: 0.9; }
        .resultado {
            margin-top: 20px;
            padding: 16px;
            background: #f8f9ff;
            border: 2px dashed #667eea;
            border-radius: 8px;
            display: none;
        }
        .resultado.visivel { display: block; }
        .resultado label { color: #667eea; }
        .copiado {
            color: #28a745;
            font-weight: 600;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Gerador de Link Base64</h1>
        <p class="subtitle">Converta qualquer URL em uma string Base64 codificada</p>

        <div class="field">
            <label for="urlInput">Cole a URL completa:</label>
            <input 
                type="text" 
                id="urlInput" 
                placeholder="https://exemplo.com/api/?"
                value="https://atxhost.com.br/api/?"
            >
        </div>

        <div class="buttons">
            <button class="btn-primary" onclick="gerar()">Gerar Link</button>
            <button class="btn-secondary" onclick="limpar()">Limpar</button>
        </div>

        <div class="resultado" id="resultado">
            <label>Resultado (Base64):</label>
            <textarea id="output" readonly></textarea>
            <div class="buttons">
                <button class="btn-primary" onclick="copiar()">📋 Copiar</button>
                <button class="btn-secondary" onclick="decodificar()">🔄 Decodificar</button>
            </div>
            <div class="copiado" id="msgCopiado"></div>
        </div>
    </div>

    <script>
        function gerar() {
            const url = document.getElementById('urlInput').value.trim();
            if (!url) {
                alert('Por favor, insira uma URL.');
                return;
            }
            try {
                // Codifica em UTF-8 e depois em Base64 (suporta acentos)
                const encoded = btoa(unescape(encodeURIComponent(url)));
                document.getElementById('output').value = encoded;
                document.getElementById('resultado').classList.add('visivel');
                document.getElementById('msgCopiado').textContent = '';
            } catch (e) {
                alert('Erro ao codificar: ' + e.message);
            }
        }

        function decodificar() {
            const b64 = document.getElementById('output').value.trim();
            if (!b64) return;
            try {
                const decoded = decodeURIComponent(escape(atob(b64)));
                document.getElementById('urlInput').value = decoded;
            } catch (e) {
                alert('String Base64 inválida.');
            }
        }

        function copiar() {
            const output = document.getElementById('output');
            output.select();
            output.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(output.value)
                .then(() => {
                    document.getElementById('msgCopiado').textContent = '✅ Copiado para a área de transferência!';
                    setTimeout(() => {
                        document.getElementById('msgCopiado').textContent = '';
                    }, 2500);
                })
                .catch(() => alert('Não foi possível copiar.'));
        }

        function limpar() {
            document.getElementById('urlInput').value = '';
            document.getElementById('output').value = '';
            document.getElementById('resultado').classList.remove('visivel');
            document.getElementById('msgCopiado').textContent = '';
        }

        // Enter para gerar
        document.getElementById('urlInput').addEventListener('keydown', e => {
            if (e.key === 'Enter') gerar();
        });
    </script>
</body>
</html>