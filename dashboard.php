<?php
include 'config.php';

// Função para obter categorias
function getCategorias($conn)
{
    $sql = "SELECT id, nome FROM categorias";
    return $conn->query($sql);
}

// Função para obter lojas
function getLojas($conn)
{
    $sql = "SELECT id, nome FROM lojas";
    return $conn->query($sql);
}

// Função para verificar se a loja já existe
function verificarLojaExistente($conn, $nome_loja)
{
    $sql = "SELECT id FROM lojas WHERE nome = '$nome_loja'";
    return $conn->query($sql)->num_rows > 0;
}

// Função para criar loja
function criarLoja($conn, $nome_loja, $imagem_loja, $link_loja)
{
    $sql = "INSERT INTO lojas (nome, link, imagem) VALUES ('$nome_loja', '$link_loja', '$imagem_loja')";
    return $conn->query($sql);
}

// Função para criar categoria
function criarCategoria($conn, $nome_categoria, $imagem_categoria)
{
    $sql = "INSERT INTO categorias (nome, imagem) VALUES ('$nome_categoria', '$imagem_categoria')";
    return $conn->query($sql);
}

// Função para verificar se o produto existe
function verificarProdutoExistente($conn, $id_produto)
{
    $sql = "SELECT id FROM produtos WHERE id_produto = '$id_produto'";
    return $conn->query($sql)->num_rows > 0;
}

// Função para criar produto
function criarProduto($conn, $categoria_id_produto, $nome_produto, $id_produto, $link_produto, $imagem_produto, $loja_produto_id)
{
    $sql = "INSERT INTO produtos (categoria_id, nome_produto, id_produto, link_produto, imagem_produto, loja_produto_id) 
            VALUES ('$categoria_id_produto', '$nome_produto', '$id_produto', '$link_produto', '$imagem_produto', '$loja_produto_id')";
    return $conn->query($sql);
}

// Conexão com o banco de dados
$servername = $env_ip;
$username = $env_user;
$password = $env_password;
$dbname = $env_db; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Criar Loja
    if (isset($_POST['criar_loja']) && !empty($_POST['nome_loja']) && !empty($_POST['imagem_loja']) && !empty($_POST['link_loja'])) {
        $nome_loja = $_POST['nome_loja'];
        $imagem_loja = $_POST['imagem_loja'];
        $link_loja = $_POST['link_loja'];

        // Verificar se a loja já existe
        if (verificarLojaExistente($conn, $nome_loja)) {
            echo "Loja já existente!";
        } else {
            // Criar Loja
            if (criarLoja($conn, $nome_loja, $imagem_loja, $link_loja)) {
                echo "Loja criada com sucesso!";
            } else {
                echo "Erro ao criar loja: " . $conn->error;
            }
        }
    }

    // Criar Categoria
    if (isset($_POST['criar_categoria']) && !empty($_POST['nome_categoria']) && !empty($_POST['imagem_categoria'])) {
        $nome_categoria = $_POST['nome_categoria'];
        $imagem_categoria = $_POST['imagem_categoria'];

        if (criarCategoria($conn, $nome_categoria, $imagem_categoria)) {
            echo "Categoria criada com sucesso!";
        } else {
            echo "Erro ao criar categoria: " . $conn->error;
        }
    }

    // Criar Produto
    if (isset($_POST['criar_produto']) && !empty($_POST['categoria_produto_id']) && !empty($_POST['id_produto']) && !empty($_POST['loja_produto_id'])) {
        $categoria_id_produto = $_POST['categoria_produto_id'];
        $nome_produto = $_POST['nome_produto'];
        $id_produto = $_POST['id_produto'];
        $link_produto = $_POST['link_produto'];
        $imagem_produto = $_POST['imagem_produto'];
        $loja_produto_id = $_POST['loja_produto_id'];

        if (verificarProdutoExistente($conn, $id_produto)) {
            echo "Produto já existe!";
        } else {
            if (criarProduto($conn, $categoria_id_produto, $nome_produto, $id_produto, $link_produto, $imagem_produto, $loja_produto_id)) {
                echo "Produto criado com sucesso!";
            } else {
                echo "Erro ao criar produto: " . $conn->error;
            }
        }
    }

}

// Reabrir a conexão para exibição das categorias na página
$resultCategorias = getCategorias($conn);

// Não feche a conexão até o final do script
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script>
        // Função para atualizar as lojas com base na categoria selecionada
        function atualizarLojas() {
            var categoria_id = document.getElementById("categoria_produto_id").value;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("loja_produto_id").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "buscarLojas.php?categoria_id=" + categoria_id, true);
            xhttp.send();
        }
    </script>
</head>

<body>

    <hr>

    <!-- Formulário para Criar Loja -->
    
    <form method="POST">
    <h2>Criar Loja</h2>
        <label for="nome_loja">Nome da Loja:</label>
        <input type="text" id="nome_loja" name="nome_loja" required><br><br>

        <label for="imagem_loja">URL da Imagem:</label>
        <input type="url" id="imagem_loja" name="imagem_loja" required><br><br>

        <label for="link_loja">Link da Loja:</label>
        <input type="url" id="link_loja" name="link_loja" required><br><br>

        <button type="submit" name="criar_loja">Criar Loja</button>
    </form>

    <!-- Formulário para Criar Categoria -->
    
    <form method="POST">
    <h2>Criar Categoria</h2>
        <label for="nome_categoria">Nome da Categoria:</label>
        <input type="text" id="nome_categoria" name="nome_categoria" required><br><br>

        <label for="imagem_categoria">URL da Imagem:</label>
        <input type="url" id="imagem_categoria" name="imagem_categoria" required><br><br>

        <button type="submit" name="criar_categoria">Criar Categoria</button>
    </form>

    <hr>

    <!-- Formulário para Criar Produto -->
    
    <form method="POST">
    <h2>Criar Produto</h2>
        <label for="categoria_produto_id">Categoria:</label>
        <select id="categoria_produto_id" name="categoria_produto_id" onchange="atualizarLojas()" required>
            <option value="">Selecione uma categoria</option>
            <?php
            // Exibir categorias
            $resultCategorias = getCategorias($conn);
            while ($row = $resultCategorias->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="nome_produto">Nome do Produto:</label>
        <input type="text" id="nome_produto" name="nome_produto" required><br><br>

        <label for="id_produto">ID do Produto:</label>
        <input type="text" id="id_produto" name="id_produto" required><br><br>

        <label for="imagem_produto">Imagem do Produto:</label>
        <input type="url" id="imagem_produto" name="imagem_produto" required><br><br>

        <label for="link_produto">Link do Produto:</label>
        <input type="url" id="link_produto" name="link_produto" required><br><br>

        <!-- Dropdown para selecionar a loja -->
        <label for="loja_produto_id">Loja:</label>
        <select id="loja_produto_id" name="loja_produto_id" required>
            <option value="">Selecione uma loja</option>
            <?php
            // Exibir lojas na dropdown
            $resultLojas = getLojas($conn);
            while ($row = $resultLojas->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" name="criar_produto">Criar Produto</button>
    </form>

</body>

</html>
