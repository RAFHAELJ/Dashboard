<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotspot Login Page</title>
  <style>
    .page-layout {
      position: relative;
      width: 100%;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .top-layout {
      width: 100%;
      height: 150px;
    }
    .input-field, .button-field, .text-field {
      position: absolute;
    }
    .button-field button, .input-field input {
      width: 100%;
      height: 100%;
    }
    .text-link {
      position: absolute;
      cursor: pointer;
      text-decoration: underline;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <div id="app"></div>

  <script>
    // Função para gerar HTML a partir do JSON
    function generateHTMLFromJSON(screenData) {
      let html = '';

      // Estrutura da página (Topo e Fundo)
      html += `<div class="page-layout" style="background: ${screenData.background_type === 'Imagem' ? 'url(' + screenData.background_value + ')' : screenData.background_value};">`;
      html += `<div class="top-layout" style="background-color: ${screenData.top_type === 'Cor' ? screenData.top_value : 'transparent'};">`;

      if (screenData.top_type === 'Imagem') {
        html += `<img src="${screenData.top_value}" alt="Topo da Página" style="width: 100%; height: 100%;">`;
      }
      
      html += `</div>`;

      // Adicionando os elementos baseados no JSON
      screenData.elements.forEach(element => {
        if (element.type === 'input') {
          html += `<div class="input-field" style="top:${element.top}px; left:${element.left}px; width:${element.width}px; height:${element.height}px;">`;
          html += `<input type="text" style="background-color: ${element.color}; border-radius: ${element.shape}px; opacity: ${element.opacity}; box-shadow: 0px ${element.elevation}px ${element.elevation}px rgba(0,0,0,0.2);" />`;
          html += `</div>`;
        }
        
        if (element.type === 'button') {
          html += `<div class="button-field" style="top:${element.top}px; left:${element.left}px; width:${element.width}px; height:${element.height}px;">`;
          html += `<button style="background-color: ${element.color}; border-radius: ${element.shape}px; opacity: ${element.opacity}; box-shadow: 0px ${element.elevation}px ${element.elevation}px rgba(0,0,0,0.2);">${element.text}</button>`;
          html += `</div>`;
        }
        
        if (element.type === 'text') {
          html += `<div class="text-field" style="top:${element.top}px; left:${element.left}px; width:${element.width}px; height:${element.height}px; color: ${element.color};">`;
          html += `<span style="font-size: ${element.fontSize || 16}px; font-weight: ${element.fontWeight}; font-style: ${element.fontStyle};">${element.text}</span>`;
          html += `</div>`;
        }

        if (element.type === 'buttonC' || element.type === 'buttonA') {
          html += `<a href="#" class="text-link" style="top:${element.top}px; left:${element.left}px; color: ${element.color}; font-size: 14px;">${element.type === 'buttonC' ? 'Criar nova conta' : 'Ajuda'}</a>`;
        }
      });

      // Fechando a div principal
      html += `</div>`;

      return html;
    }

    // Carregar o arquivo JSON via fetch
    fetch('{{ asset('login_customization.json') }}')
      .then(response => response.json())
      .then(data => {
        const generatedHTML = generateHTMLFromJSON(data);
        document.getElementById('app').innerHTML = generatedHTML;
      })
      .catch(error => console.error('Erro ao carregar o JSON:', error));
  </script>
</body>
</html>
