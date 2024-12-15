<?php
include '../config.php';

// Verificar a autenticação do token
$access_token = 'SEU_ACCESS_TOKEN';

// Endpoint para responder à DM
$url = "https://graph.instagram.com/v12.0/me/messages?access_token=$access_token";

// Dados recebidos da Webhook
$data = json_decode(file_get_contents('php://input'), true);
$comment = $data['entry'][0]['changes'][0]['value'];
$user_id = $comment['from']['id'];
$post_id = $comment['parent_id'];

// Consultar a mensagem associada ao post_id
$query = "SELECT message FROM post_messages WHERE post_id = '$post_id'";
$result = $conn->query($query);
$message = $result->fetch_assoc()['message'];

// Função para registrar logs no banco de dados
function logAction($conn, $post_id, $user_id, $message, $status, $error = null) {
    $post_id = $conn->real_escape_string($post_id);
    $user_id = $conn->real_escape_string($user_id);
    $message = $conn->real_escape_string($message);
    $status = $conn->real_escape_string($status);
    $error = $conn->real_escape_string($error);

    $sql = "INSERT INTO message_logs (post_id, user_id, message, status, error, created_at) VALUES ('$post_id', '$user_id', '$message', '$status', '$error', NOW())";
    $conn->query($sql);
}

if ($message) {
    // Enviar a mensagem
    $data = [
        'recipient' => ['id' => $user_id],
        'message' => ['text' => $message]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    // Log ou retorno do resultado da mensagem
    $log = json_decode($response, true);
    if (isset($log['error'])) {
        $error_message = $log['error']['message'];
        logAction($conn, $post_id, $user_id, $message, 'failed', $error_message);
    } else {
        logAction($conn, $post_id, $user_id, $message, 'success');
    }
} else {
    echo 'Mensagem vinculada não encontrada.';
    logAction($conn, $post_id, $user_id, 'N/A', 'error', 'Mensagem não encontrada');
}

$conn->close();
?>
