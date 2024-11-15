<?php
// Configurações do banco de dados
$host = "localhost"; // Em HostGator, geralmente é "localhost"
$user = "linkit58_admin"; // Substitua pelo usuário do banco de dados
$password = "^+(<E;Mf%0QFVVT"; // Substitua pela senha do banco
$database = "linkit58_main"; // Substitua pelo nome do banco de dados

// Conexão com o MySQL
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão bem-sucedida!<br>";

// Testar uma consulta simples
$sql = "SELECT NOW() AS agora";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Data e hora atual do servidor: " . $row['agora'];
    }
} else {
    echo "Nenhum dado encontrado.";
}

// Fechar conexão
$conn->close();
?>
