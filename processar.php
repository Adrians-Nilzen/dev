<?php

// Verifica se o parâmetro 'link' foi passado na URL
if (isset($_GET['link'])) {
    
    $link_completo = $_GET['link'];
    $padrao = '/assinar\/([a-zA-Z0-9]{32})/';
    
    if (preg_match($padrao, $link_completo, $matches)) {
        
        $assinatura_id = $matches[1];
        $url_conteudo = "https://assinadordigitalexterno.praiagrande.sp.gov.br/sign/pades/signers/" . $assinatura_id;

        // Tenta obter o conteúdo da página HTML inteira
        $conteudo_html = @file_get_contents($url_conteudo);
        
        if ($conteudo_html === false) {
            echo "Erro ao buscar os dados. Verifique o link ou a conexão.";
        } else {
            // Usa expressão regular para encontrar e extrair o conteúdo dentro da tag <pre>
            if (preg_match('/<pre>\[.*\]<\/pre>/s', $conteudo_html, $matches)) {
                // Remove as tags <pre> do resultado
                $conteudo_json = str_replace(['<pre>', '</pre>'], '', $matches[0]);

                // Decodifica o conteúdo JSON
                $dados = json_decode($conteudo_json, true);
                
                if ($dados && is_array($dados)) {
                    // Início do HTML de exibição
                    echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Signatários do Documento</title><style>body { font-family: Arial, sans-serif; padding: 20px; } .container { max-width: 600px; margin: 0 auto; } .resultado { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; } h3 { margin-top: 0; }</style></head><body><div class='container'><h2>Signatários do Documento</h2>";
                    
                    // Itera e exibe os dados
                    foreach ($dados as $signatario) {
                        $nome = htmlspecialchars($signatario['responsavel']);
                        $data_hora_bruta = new DateTime($signatario['signingTime']);
                        $data_hora_formatada = $data_hora_bruta->format('d/m/Y \à\s H:i:s');
                        
                        echo "<div class='resultado'><h3>" . $nome . "</h3><p>Assinado em: " . $data_hora_formatada . "</p></div>";
                    }
                    
                    echo "</div></body></html>";
                } else {
                    echo "Não foi possível processar os dados. O formato está inválido.";
                }
            } else {
                echo "Nenhum conteúdo JSON encontrado na página.";
            }
        }
    } else {
        echo "Link inválido. Por favor, use o formato correto.";
    }
} else {
    // Instruções de uso
    echo "<h2>Verificador de Signatários</h2><p>Para usar, adicione o link do documento na URL, como neste exemplo:</p><p><code>http://seusite.com/verificador.php?link=https://assinadordigitalexterno.praiagrande.sp.gov.br/assinar/PINKVON4XDYAWRIN7FRMEJMTZOQIGLAW</code></p>";
}
?>