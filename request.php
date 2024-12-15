<?php
$access_token = 'IGAAW5GMlnC7xBZAE9sTGtQUGdkdGJuX2hUdVRpSWhJX2JxNGoyZA1h1WlgtUjhabzM4aEJTU1BRdVJCZA3BWXy1YeTVGVjJpcWtKanJzVnFvc3lad0cwODBqN1FLVnk5QlB3QkVxUjVmUWYyajFCSGtTd1RqT1FKZA0lNN1UtTDFyRQZDZD'; // Substitua com seu token

// URL para obter posts
$url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,timestamp&access_token={$access_token}";

// Requisição CURL para obter os posts
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Erro: ' . curl_error($ch);
} else {
    $data = json_decode($response, true);
    if (isset($data['error'])) {
        echo 'Erro na API: ' . $data['error']['message'];
    } else {
        foreach ($data['data'] as $post) {
            echo 'ID do Post: ' . $post['id'] . '<br>';
            echo 'Legenda: ' . ($post['caption'] ?? 'Sem legenda') . '<br>';
            echo 'Tipo de Mídia: ' . $post['media_type'] . '<br>';
            echo 'URL da Mídia: ' . $post['media_url'] . '<br>';
            echo 'Data/Hora: ' . $post['timestamp'] . '<br><br>';

            // URL para obter comentários para cada post individual
            $comments_url = "https://graph.instagram.com/{$post['id']}/comments?fields=id,text,created_time&access_token={$access_token}";

            // Requisição CURL para obter os comentários
            $ch_comments = curl_init();
            curl_setopt($ch_comments, CURLOPT_URL, $comments_url);
            curl_setopt($ch_comments, CURLOPT_RETURNTRANSFER, 1);

            $response_comments = curl_exec($ch_comments);

            if (curl_errno($ch_comments)) {
                echo 'Erro nos comentários: ' . curl_error($ch_comments);
            } else {
                $data_comments = json_decode($response_comments, true);
                if (isset($data_comments['data']) && count($data_comments['data']) > 0) {
                    echo 'Comentários: <br>';
                    foreach ($data_comments['data'] as $comment) {
                        echo 'ID do Comentário: ' . $comment['id'] . '<br>';
                        echo 'Texto: ' . $comment['text'] . '<br>';
                        echo 'Data/Hora do Comentário: ' . $comment['created_time'] . '<br><br>';
                    }
                } else {
                    echo 'Nenhum comentário encontrado.<br><br>';
                }
            }
            curl_close($ch_comments);
        }
    }
}
curl_close($ch);

?>
