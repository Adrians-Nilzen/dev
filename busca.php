<?php
// proxy.php
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Parâmetro URL é obrigatório';
    exit;
}

$url = $_GET['url'];

// Validação básica (pode melhorar para segurança)
if (strpos($url, 'assinadordigitalexterno.praiagrande.sp.gov.br') === false) {
    http_response_code(403);
    echo 'URL não permitida';
    exit;
}

// Busca o conteúdo da URL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Dependendo do servidor pode ser necessário
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    http_response_code($httpcode);
    echo "Erro ao buscar conteúdo: HTTP $httpcode";
    exit;
}

header('Content-Type: application/json');
echo $response;
?>
