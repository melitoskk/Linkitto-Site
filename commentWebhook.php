<?php
// Substitua pelo token de verificação fornecido pelo Instagram
$verification_token = 'linkittofoda';

// Substitua pelo seu access token
$access_token = 'IGAAW5GMlnC7xBZAE9sTGtQUGdkdGJuX2hUdVRpSWhJX2JxNGoyZA1h1WlgtUjhabzM4aEJTU1BRdVJCZA3BWXy1YeTVGVjJpcWtKanJzVnFvc3lad0cwODBqN1FLVnk5QlB3QkVxUjVmUWYyajFCSGtTd1RqT1FKZA0lNN1UtTDFyRQZDZD';

// Arquivo para registrar logs
$log_file = __DIR__ . '/webhook_log.txt';

// Captura os dados recebidos via POST
$input = json_decode(file_get_contents('php://input'), true);

// Registra o conteúdo recebido no log
file_put_contents($log_file, "Recebido: " . json_encode($input, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

// Verifica se o evento é um comentário
if (isset($input['entry'])) {
    foreach ($input['entry'] as $entry) {
        foreach ($entry['changes'] as $change) {
            if ($change['field'] === 'comments') {
                $comment_id = $change['value']['id'];
                $post_id = $change['value']['parent_id'];

                // Log do comentário
                file_put_contents($log_file, "Comentário detectado - ID da Postagem: $post_id, ID do Comentário: $comment_id" . PHP_EOL, FILE_APPEND);

                // Exibe os IDs
                echo "ID da Postagem: $post_id <br>";
                echo "ID do Comentário: $comment_id <br>";

                // Aqui você pode usar esses IDs para realizar alguma ação, como salvar em um banco de dados ou chamar outra API
            }
        }
    }
}

// Verificação do token de validação
if (isset($_GET['hub_challenge']) && $_GET['hub_verify_token'] === $verification_token) {
    file_put_contents($log_file, "Verificação de token recebida: {$_GET['hub_challenge']}" . PHP_EOL, FILE_APPEND);
    echo $_GET['hub_challenge'];
    exit;
}
?>
