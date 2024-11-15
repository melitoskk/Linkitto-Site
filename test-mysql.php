<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "usuario_nomeUsuario";
$password = "senha_do_banco";
$database = "usuario_nomeDoBanco";

// Testar conexão
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
echo "Conexão bem-sucedida!";
?>
