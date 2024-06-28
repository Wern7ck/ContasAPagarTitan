<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Conta a Pagar</title>
</head>
<body>
    <h2>Editar Conta a Pagar</h2>
    <?php
    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'projeto_titan';
    $conexao = new mysqli($host, $usuario, $senha, $banco);

    // Verifica se houve erro na conexão

    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Verificar se foi enviado o ID da conta a pagar

    if (isset($_GET['id_conta_pagar'])) {
        $id_conta_pagar = $_GET['id_conta_pagar'];
        
        // Consultar a conta a pagar pelo ID
        
        $query_conta = "SELECT * FROM tbl_conta_pagar WHERE id_conta_pagar = $id_conta_pagar";
        $result_conta = $conexao->query($query_conta);

        if ($result_conta->num_rows == 1) {
            $conta = $result_conta->fetch_assoc();
            echo '<form action="processar_conta.php" method="POST">';
            echo '<input type="hidden" name="id_conta_pagar" value="' . $conta["id_conta_pagar"] . '">';
            echo 'Data de Pagamento: <input type="date" name="nova_data_pagar" value="' . $conta["data_pagar"] . '" required><br><br>';
            echo 'Valor a Pagar: <input type="number" name="novo_valor" step="0.01" value="' . $conta["valor"] . '" required><br><br>';
            echo '<button type="submit" name="editar">Salvar Alterações</button>';
            echo '</form>';
        } else {
            echo "Conta não encontrada.";
        }
    } else {
        echo "ID da conta não especificado.";
    }

    $conexao->close();
    ?>
</body>
</html>
