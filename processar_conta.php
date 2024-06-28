<?php
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'projeto_titan';
$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die("Erro na conexão:" . $conexao->connect_error);
}

date_default_timezone_set('America/Sao_Paulo');


// nova conta

if (isset($_POST['submit'])) {
    $id_empresa = $_POST['id_empresa'];
    $data_pagar = $_POST['data_pagar'];
    $valor = $_POST['valor'];

    // Inserir no banco de dados
    $query_inserir_conta = "INSERT INTO tbl_conta_pagar (id_empresa, data_pagar, valor) 
                            VALUES ('$id_empresa', '$data_pagar', '$valor')";
    if ($conexao->query($query_inserir_conta) === TRUE) {
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao inserir conta a pagar: " . $conexao->error;
    }
}


// Excluir conta 

if (isset($_POST['excluir'])) {
    $id_conta_pagar = $_POST['id_conta_pagar'];
    $query_excluir = "DELETE FROM tbl_conta_pagar WHERE id_conta_pagar = $id_conta_pagar";
    if ($conexao->query($query_excluir) === TRUE) {
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao excluir conta a pagar: " . $conexao->error;
    }
}

// Editar conta
if (isset($_POST['editar'])) {
    $id_conta_pagar = $_POST['id_conta_pagar'];
    $nova_data_pagar = $_POST['nova_data_pagar'];
    $novo_valor = $_POST['novo_valor'];

    // Atualizar no banco de dados

    $query_editar = "UPDATE tbl_conta_pagar 
                     SET data_pagar = '$nova_data_pagar', valor = '$novo_valor' 
                     WHERE id_conta_pagar = $id_conta_pagar";
    if ($conexao->query($query_editar) === TRUE) {
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao editar conta a pagar: " . $conexao->error;
    }
}

// Marcar conta como paga

if (isset($_POST['marcar_pago'])) {
    $id_conta_pagar = $_POST['id_conta_pagar'];
    
    // Consultar a conta a pagar para obter a data de pagamento e o valor

    $query_conta_pagar = "SELECT id_empresa, data_pagar, valor FROM tbl_conta_pagar WHERE id_conta_pagar = $id_conta_pagar";
    $result_conta_pagar = $conexao->query($query_conta_pagar);

    if ($result_conta_pagar->num_rows > 0) {
        $row = $result_conta_pagar->fetch_assoc();
        
        $data_pagar = $row['data_pagar'];
        $valor = $row['valor'];
        
        // Obter a data atual
        $data_atual = date('d-m-Y');
        
        // Calcular o valor com desconto ou acréscimo
        if ($data_atual < $data_pagar) {
            // Pagamento antecipado (desconto de 5%)
            $valor_pago = $valor - ($valor * 0.05);
        } elseif ($data_atual > $data_pagar) {
            // Pagamento atrasado (acréscimo de 10%)
            $valor_pago = $valor + ($valor * 0.10);
        } else {
            // Pagamento no dia (sem alteração no valor)
            $valor_pago = $valor;
        }
        
        // Atualizar a data de pagamento e o valor pago na tabela

        $data_pagamento = date('d-m-Y H:i:s'); 
        
        $query_marcar_pago = "UPDATE tbl_conta_pagar SET pago = 1, data_pagamento = '$data_pagamento', valor_pago = '$valor_pago' WHERE id_conta_pagar = $id_conta_pagar";
        
        if ($conexao->query($query_marcar_pago) === TRUE) {
            header('Location: index.php');
            exit;
        } else {
            echo "Erro ao marcar conta como pago: " . $conexao->error;
        }
    } else {
        echo "Conta a pagar não encontrada.";
    }
}

$conexao->close();
?>