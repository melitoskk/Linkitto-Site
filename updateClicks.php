<?php
include 'config.php';

$servername = $env_ip;
$username = "linkit58_admin";
$password = "^+(<E;Mf%0QFVVT";
$dbname = "linkit58_main";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id_produto'])) {
    $id_produto = intval($_POST['id_produto']);
    $sql = "UPDATE produtos SET clicks = clicks + 1 WHERE id_produto = $id_produto";

    if ($conn->query($sql) === TRUE) {
        echo "Clique registrado.";
    } else {
        echo "Erro ao registrar clique: " . $conn->error;
    }
} else {
    echo "ID do produto não enviado.";
}

$conn->close();
?>
