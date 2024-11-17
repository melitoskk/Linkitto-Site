<?php
// Verificar se o parâmetro id_produto foi passado
if (isset($_POST['id_produto'])) {
    $id_produto = (int) $_POST['id_produto'];

    // Conexão com o banco de dados
    include 'config.php';
    $conn = new mysqli($env_ip, "linkit58_admin", "^+(<E;Mf%0QFVVT", "linkit58_main");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Atualizar os cliques do produto
    $sql = "UPDATE produtos SET clicks = clicks + 1 WHERE id_produto = $id_produto";
    
    if ($conn->query($sql) === TRUE) {
        echo "Clique registrado!";
    } else {
        echo "Erro ao registrar clique: " . $conn->error;
    }

    $conn->close();
}
?>
