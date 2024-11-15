<?php
include 'config.php';

// Conexão com o banco de dados
$servername = $env_ip;  // ou IP do servidor, se for remoto
$username = "linkit58_admin";  // Nome do usuário MySQL
$password = "^+(<E;Mf%0QFVVT";  // Senha do usuário MySQL
$dbname = "linkit58_main"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegar o nome da categoria
    $categoria_nome = $_POST['categoria_nome'];

    // Verificar se a categoria já existe no banco de dados
    $sql_check = "SELECT * FROM categorias WHERE nome = '$categoria_nome'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Categoria já existe
        echo "A categoria '$categoria_nome' já foi criada.";
    } else {
        // Inserir a categoria no banco de dados
        $sql = "INSERT INTO categorias (nome) VALUES ('$categoria_nome')";

        if ($conn->query($sql) === TRUE) {
            echo "Categoria '$categoria_nome' criada com sucesso!";
        } else {
            echo "Erro ao criar categoria: " . $conn->error;
        }
    }
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Criação de Categoria</title>
</head>
<body>
    <h1>Criação de Categoria</h1>
    <form method="POST" action="">
        <label for="categoria_nome">Nome da Categoria:</label>
        <input type="text" id="categoria_nome" name="categoria_nome" placeholder="Nome da categoria" required><br><br>

        <button type="submit">Criar Categoria</button>
    </form>
</body>
</html>
