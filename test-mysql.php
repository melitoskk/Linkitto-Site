<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "linkit58_admin";
$password = "^+(<E;Mf%0QFVVT";
$database = "linkit58_main";

// Testar conexão
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
echo "Conexão bem-sucedida!";
?>

$host = "localhost"; // Em HostGator, geralmente é "localhost"
$user = "linkit58_admin"; // Substitua pelo usuário do banco de dados
$password = "^+(<E;Mf%0QFVVT"; // Substitua pela senha do banco
$database = "linkit58_main"; // Substitua pelo nome do banco de dados