<?php
// Defina as variáveis de conexão
$servername = "50.116.87.79";  // ou IP do servidor, se for remoto
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
    // Pegar dados do formulário
    $categoria_id = $_POST['categoria_id'];
    $numero_episodio = $_POST['numero_episodio'];
    $thumb_url = $_POST['thumb_url'];

    // Verificar se o episódio já existe
    $sql_check = "SELECT * FROM episodios WHERE categoria_id = '$categoria_id' AND numero_episodio = '$numero_episodio'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "Episódio já existe para esta categoria e número.";
    } else {
        // Inserir novo episódio
        $sql_insert = "INSERT INTO episodios (categoria_id, numero_episodio, thumb_url) 
                       VALUES ('$categoria_id', '$numero_episodio', '$thumb_url')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Episódio criado com sucesso!";
        } else {
            echo "Erro ao criar episódio: " . $conn->error;
        }
    }
}

// Recuperar as categorias do banco de dados para preencher o dropdown
$sql_categoria = "SELECT id, nome FROM categorias";
$result_categoria = $conn->query($sql_categoria);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação de Episódio</title>
</head>
<body>
    <h1>Criação de Episódio</h1>
    <form method="POST" action="">
        <!-- Dropdown para categorias -->
        <label for="categoria_id">Selecione a Categoria:</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <?php
            // Preencher a dropdown com as categorias do banco
            if ($result_categoria->num_rows > 0) {
                while($row = $result_categoria->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                }
            } else {
                echo "<option value=''>Nenhuma categoria encontrada</option>";
            }
            ?>
        </select><br><br>

        <!-- Número do episódio -->
        <label for="numero_episodio">Número do Episódio:</label>
        <input type="number" id="numero_episodio" name="numero_episodio" required><br><br>

        <!-- URL da thumb -->
        <label for="thumb_url">URL da Thumbnail:</label>
        <input type="url" id="thumb_url" name="thumb_url" required><br><br>

        <button type="submit">Criar Episódio</button>
    </form>
</body>
</html>
