<?php
$access_token = 'IGAAW5GMlnC7xBZAE9sTGtQUGdkdGJuX2hUdVRpSWhJX2JxNGoyZA1h1WlgtUjhabzM4aEJTU1BRdVJCZA3BWXy1YeTVGVjJpcWtKanJzVnFvc3lad0cwODBqN1FLVnk5QlB3QkVxUjVmUWYyajFCSGtTd1RqT1FKZA0lNN1UtTDFyRQZDZD'; // Substitua com seu token

// URL da API do Instagram para obter informações do usuário
$url = "https://graph.instagram.com/me?fields=id,username&access_token={$access_token}";

// Requisição CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// Verificar erros na requisição
if (curl_errno($ch)) {
    echo 'Erro: ' . curl_error($ch);
} else {
    $data = json_decode($response, true);
    if (isset($data['error'])) {
        echo 'Erro na API: ' . $data['error']['message'];
    } else {
        echo 'Usuário ID: ' . $data['id'] . '<br>';
        echo 'Nome de usuário: ' . $data['username'];
    }
}
curl_close($ch);
?>
