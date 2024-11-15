<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "linkit58_admin";
$password = "^+(<E;Mf%0QFVVT";
$database = "^+(<E;Mf%0QFVVT";

// Testar conexão
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
echo "Conexão bem-sucedida!";
?>