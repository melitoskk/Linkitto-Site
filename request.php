<?php
$access_token = 'SEU_ACCESS_TOKEN';

// URL do vídeo
$video_url = 'https://www.instagram.com/p/DDlp8EKszrf/';

// Extrair a `id` do post da URL
$exploded_url = explode('/', $video_url);
$post_id = end($exploded_url);

// URL para obter informações sobre o post
$post_url = "https://graph.instagram.com/{$post_id}?fields=id,caption,media_url&access_token={$access_token}";

// Requisição CURL para obter informações do post
$ch_post = curl_init();
curl_setopt($ch_post, CURLOPT_URL, $post_url);
curl_setopt($ch_post, CURLOPT_RETURNTRANSFER, 1);

$response_post = curl_exec($ch_post);

if (curl_errno($ch_post)) {
    echo 'Erro ao obter informações do post: ' . curl_error($ch_post);
} else {
    $data_post = json_decode($response_post, true);
    if (isset($data_post['error'])) {
        echo 'Erro na API: ' . $data_post['error']['message'];
    } else {
        echo 'ID do Post: ' . $data_post['id'] . '<br>';
        echo 'Legenda: ' . ($data_post['caption'] ?? 'Sem legenda') . '<br>';
        echo 'URL da Mídia: ' . $data_post['media_url'] . '<br>';
    }
}
curl_close($ch_post);
?>
