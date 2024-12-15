<?php

include '../config.php';
// Configuração de API e acesso ao token do Instagram
$access_token = 'IGQWRPbzNtV05oN2x4ZA19DN1YydGRoMlFOOFVnRHFkTzhrcnNXdWNQd1hDWnZAZAZAjg5bk5mTzBObFRFOHpvcWhtWXdiMHJwdGR5Rk1jZADI5cTVlSzlQMUtvMGRoV3VaeWlnSXdINGhaV1pYQmNfUTJ6SUEwWTRjQVUZD'; // Substitua pelo seu token do Instagram

// URL da API do Instagram para recuperar os posts
$url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,timestamp&access_token=$access_token";

// Realizar a requisição usando cURL
$response = file_get_contents($url);
$data = json_decode($response, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['post_id']) && isset($_POST['message'])) {
        $post_id = $_POST['post_id'];
        $message = $conn->real_escape_string($_POST['message']);

        // Inserir os dados no banco de dados
        $sql = "INSERT INTO post_messages (post_id, message) VALUES ('$post_id', '$message')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Mensagem salva com sucesso!";
        } else {
            $error_message = "Erro ao salvar mensagem: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts do Instagram com Mensagem</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        .post {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            overflow: hidden;
            width: 300px;
        }
        .post img, .post video {
            width: 100%;
        }
        .post .caption {
            padding: 1rem;
        }
        .input-container {
            display: flex;
            align-items: center;
            margin-top: 1rem;
        }
        .input-container input {
            width: calc(100% - 60px);
            padding: 0.8rem;
            margin-right: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .input-container button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .input-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Posts Recentes do Instagram com Mensagem</h1>

    <?php
    if (!empty($data['data'])) {
        foreach ($data['data'] as $post) {

            // Consultar se já existe uma mensagem vinculada ao post
            $query = "SELECT message FROM post_messages WHERE post_id = '{$post['id']}'";
            $result = $conn->query($query);

            $message = $result->num_rows > 0 ? $result->fetch_assoc()['message'] : '';

            echo '<div class="post">';
            if ($post['media_type'] === 'IMAGE' || $post['media_type'] === 'CAROUSEL_ALBUM') {
                echo '<img src="' . $post['media_url'] . '" alt="Post do Instagram">';
            } elseif ($post['media_type'] === 'VIDEO') {
                echo '<video controls><source src="' . $post['media_url'] . '" type="video/mp4"></video>';
            }
            echo '<div class="caption">' . htmlspecialchars($post['caption']) . '</div>';
            ?>
            <div class="input-container">
                <form method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <input type="text" name="message" placeholder="Digite sua mensagem..." value="<?php echo htmlspecialchars($message); ?>" required>
                    <button type="submit">Salvar</button>
                </form>
            </div>
            <?php
            echo '</div>';
        }
    } else {
        echo '<p>Nenhum post encontrado.</p>';
    }

    if (isset($success_message)) {
        echo "<p style='color: green;'>$success_message</p>";
    }
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

</body>
</html>
