<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Criação de Produto</title>
    <script>
    function atualizarEpisodios() {
        var categoria_id = document.getElementById("categoria_id").value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("episodio_id").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "buscarEpisodios.php?categoria_id=" + categoria_id, true);
        xhttp.send();
    }
    </script>
</head>
<body>
    <h1>Formulário de Criação de Produto</h1>
    
    <form method="POST" action="criarProduto.php">
        <!-- Dropdown para Categorias -->
        <label for="categoria_id">Categoria:</label>
        <select id="categoria_id" name="categoria_id" onchange="atualizarEpisodios()">
            <option value="">Selecione uma categoria</option>
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

            // Buscar categorias do banco de dados
            $resultCategorias = $conn->query("SELECT id, nome FROM categorias");
            while ($row = $resultCategorias->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
            }

            // Fechar a conexão com o banco de dados
            $conn->close();
            ?>
        </select><br><br>

        <!-- Dropdown para Episódios -->
        <label for="episodio_id">Episódio:</label>
        <select id="episodio_id" name="episodio_id">
            <option value="">Selecione um episódio</option>
        </select><br><br>

        <!-- Nome do Produto -->
        <label for="nome_produto">Nome do Produto:</label>
        <input type="text" id="nome_produto" name="nome_produto" required><br><br>

        <!-- ID do Produto -->
        <label for="id_produto">ID do Produto:</label>
        <input type="text" id="id_produto" name="id_produto" required><br><br>

        <!-- Link do Produto -->
        <label for="link_produto">Link do Produto:</label>
        <input type="url" id="link_produto" name="link_produto" required><br><br>

        <!-- Imagem do Produto -->
        <label for="imagem_produto">Imagem do Produto:</label>
        <input type="url" id="imagem_produto" name="imagem_produto" required><br><br>

        <button type="submit">Criar Produto</button>
    </form>
</body>
</html>
<?php
include 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    $servername = $env_ip;
    $username = "linkit58_admin";
    $password = "^+(<E;Mf%0QFVVT";
    $dbname = "linkit58_main";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Receber os dados do formulário
    $categoria_id = $_POST['categoria_id'];
    $episodio_id = $_POST['episodio_id'];
    $nome_produto = $_POST['nome_produto'];
    $id_produto = $_POST['id_produto'];
    $link_produto = $_POST['link_produto'];
    $imagem_produto = $_POST['imagem_produto'];

    // Verificar se o produto já existe
    $checkSql = "SELECT id FROM produtos WHERE id_produto = '$id_produto'";
    $resultCheck = $conn->query($checkSql);

    if ($resultCheck->num_rows > 0) {
        echo "Produto já existe!";
    } else {
        // Inserir o produto no banco de dados
        $sql = "INSERT INTO produtos (categoria_id, episodio_id, nome_produto, id_produto, link_produto, imagem_produto) 
                VALUES ('$categoria_id', '$episodio_id', '$nome_produto', '$id_produto', '$link_produto', '$imagem_produto')";

        if ($conn->query($sql) === TRUE) {
            echo "Produto criado com sucesso!";
        } else {
            echo "Erro: " . $conn->error;
        }
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}
?>
