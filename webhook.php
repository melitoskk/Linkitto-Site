<?php
// Verificação inicial do webhook
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_challenge'])) {
    if ($_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === 'linkittofoda') {
        echo $_GET['hub_challenge'];
        exit;
    }
    http_response_code(403);
    exit;
}

// Receber notificações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    file_put_contents('webhook_log.txt', $data, FILE_APPEND);
    http_response_code(200);
}
?>
