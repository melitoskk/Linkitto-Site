<?php
// Substitua pelo token de verificação fornecido pelo Instagram
$verification_token = 'linkittofoda';

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
                $comment_data = $change['value'];

                // Extrai os detalhes do comentário
                $comment_id = $comment_data['id'] ?? 'N/A';
                $parent_id = $comment_data['parent_id'] ?? 'N/A';
                $text = $comment_data['text'] ?? 'N/A';
                $from_user = $comment_data['from']['username'] ?? 'Unknown';
                $media_id = $comment_data['media']['id'] ?? 'Unknown';
                $media_type = $comment_data['media']['media_product_type'] ?? 'Unknown';

                // Log dos detalhes do comentário
                $log_message = "Comentário detectado: \n" .
                               "Usuário: $from_user\n" .
                               "Texto: $text\n" .
                               "ID do Comentário: $comment_id\n" .
                               "ID do Pai: $parent_id\n" .
                               "ID da Mídia: $media_id\n" .
                               "Tipo de Mídia: $media_type\n";
                file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);

                // Exibe os detalhes (apenas para depuração, pode ser removido)
                echo "Comentário detectado:<br>";
                echo "Usuário: $from_user<br>";
                echo "Texto: $text<br>";
                echo "ID do Comentário: $comment_id<br>";
                echo "ID do Pai: $parent_id<br>";
                echo "ID da Mídia: $media_id<br>";
                echo "Tipo de Mídia: $media_type<br>";

                // Aqui você pode usar esses dados para acionar outras funções, como salvar no banco ou notificar.
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
