<?php
include 'config.php';

// Conexão com o banco de dados
$servername = $env_ip;
$username = "linkit58_admin";
$password = "^+(<E;Mf%0QFVVT";
$dbname = "linkit58_main";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receber a categoria selecionada via GET
$categoria_id = $_GET['categoria_id'];

// Buscar os episódios para a categoria selecionada
$sql = "SELECT id, numero_episodio FROM episodios WHERE categoria_id = '$categoria_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Exibir os episódios encontrados
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['numero_episodio'] . "</option>";
    }
} else {
    echo "<option value=''>Nenhum episódio encontrado</option>";
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
