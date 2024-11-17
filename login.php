<?php
session_start();
include 'config.php';  // Inclui as variáveis de configuração

// Criar a conexão com o banco de dados usando as variáveis do config.php
$conn = new mysqli($env_ip, $env_user, $env_password, $env_db);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o usuário já está logado
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $usuarioDigitado = $_POST['usuario'];
    $senhaDigitada = $_POST['senha'];

    // Consulta no banco de dados para verificar o usuário
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuarioDigitado'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar se a senha corresponde
        if (password_verify($senhaDigitada, $usuario['senha'])) {
            // Criar a sessão e redirecionar para a dashboard
            $_SESSION['usuario'] = $usuario['usuario'];
            echo "<script>
                    localStorage.setItem('usuario_logado', '$usuarioDigitado');
                    window.location.href = 'dashboard.php';
                  </script>";
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($erro)) { echo "<p style='color: red;'>$erro</p>"; } ?>

    <form method="POST" action="login.php">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>
