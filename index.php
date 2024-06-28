<!DOCTYPE html>
<?php date_default_timezone_set('America/Sao_Paulo'); ?>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contas a Pagar / Vinicius Werneck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Adicionar Conta a Pagar</h2>
    <form action="processar_conta.php" method="POST">
        <?php

        // Conexão com o banco de dados (substitua pelos seus dados de conexão)

        $host = 'localhost';
        $usuario = 'root';
        $senha = '';
        $banco = 'projeto_titan';
        $conexao = new mysqli($host, $usuario, $senha, $banco);
        
        // Verifica se houve erro na conexão

        if ($conexao->connect_error) {
            die("Erro na conexão: " . $conexao->connect_error);
        }
        
        // Consulta para obter as empresas cadastradas

        $query_empresas = "SELECT id_empresa, nome FROM tbl_empresa";
        $result_empresas = $conexao->query($query_empresas);
        
        if ($result_empresas->num_rows > 0) {
            echo '<label for="id_empresa">Empresa:</label>';
            echo '<select id="id_empresa" name="id_empresa" required>';
            while ($row = $result_empresas->fetch_assoc()) {
                echo '<option value="' . $row["id_empresa"] . '">' . $row["nome"] . '</option>';
            }
            echo '</select><br><br>';
        } else {
            echo "Não há empresas cadastradas.";
        }
        ?>
        <label for="data_pagar">Data de Pagamento:</label>
        <input type="date" id="data_pagar" name="data_pagar" required><br><br>
        <input type="hidden" name="id_conta_pagar" value="<?php echo $row["id_conta_pagar"]; ?>">
        <label for="valor">Valor a Pagar:</label>
        <input type="number" id="valor" name="valor" step="0.01" required><br><br>
        <button type="submit" name="submit">Inserir</button>
    </form>
    
    <hr>
    
    <h2>Filtrar Contas a Pagar</h2>
    <form action="index.php" method="GET">
        <label for="filtro_empresa">Filtrar por Nome da Empresa:</label>
        <select id="filtro_empresa" name="filtro_empresa">
            <option value="">Todos</option>
            <?php

            // Consulta para obter as empresas cadastradas

            $query_empresas = "SELECT id_empresa, nome FROM tbl_empresa";
            $result_empresas = $conexao->query($query_empresas);
            
            if ($result_empresas->num_rows > 0) {
                while ($row = $result_empresas->fetch_assoc()) {
                    $selected = (isset($_GET['filtro_empresa']) && $_GET['filtro_empresa'] == $row['id_empresa']) ? 'selected' : '';
                    echo '<option value="' . $row["id_empresa"] . '" ' . $selected . '>' . $row["nome"] . '</option>';
                }
            }
            ?>
            </select>
            
            <label for="filtro_valor">Filtrar por Valor:</label>
            <select id="filtro_valor" name="filtro_valor_condicao">
            <option value="todos">Todos</option>
            <option value="maior" <?php if (isset($_GET['filtro_valor_condicao']) && $_GET['filtro_valor_condicao'] == 'maior') echo 'selected'; ?>>Maior que</option>
            <option value="menor" <?php if (isset($_GET['filtro_valor_condicao']) && $_GET['filtro_valor_condicao'] == 'menor') echo 'selected'; ?>>Menor que</option>
            <option value="igual" <?php if (isset($_GET['filtro_valor_condicao']) && $_GET['filtro_valor_condicao'] == 'igual') echo 'selected'; ?>>Igual a</option>
            </select>
            <input type="number" name="filtro_valor" step="0.01" value="<?php echo isset($_GET['filtro_valor']) ? $_GET['filtro_valor'] : ''; ?>">
            
            <label for="filtro_data">Filtrar por Data de Pagamento:</label>
            <input type="date" id="filtro_data" name="filtro_data" value="<?php echo isset($_GET['filtro_data']) ? $_GET['filtro_data'] : ''; ?>">
            
            <button type="submit" name="filtrar">Filtrar</button>
            </form>
            
            
            
            
            <hr>
            
    <h2>Contas a Pagar</h2>
    <table>
    <thead>
    <tr>
    <th>Empresa</th>
    <th>Data de Pagamento</th>
    <th>Valor</th>
    <th>Status</th>
    <th>Data de Pagamento Efetivo</th>
    <th>Valor Pago</th>
    <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php

    // Consulta para obter as contas a pagar

    $query_contas = "SELECT cp.id_conta_pagar, e.nome AS empresa, cp.data_pagar, cp.valor, cp.pago, cp.data_pagamento, cp.valor_pago 
                 FROM tbl_conta_pagar AS cp
                 INNER JOIN tbl_empresa AS e ON cp.id_empresa = e.id_empresa";
                 $where_clause = array();

        // adicionar filtros
        if (isset($_GET['filtrar'])) {
        
        
            // Filtro por nome da empresa
    
            if (!empty($_GET['filtro_empresa'])) {
            $id_empresa = $_GET['filtro_empresa'];
            $where_clause[] = "cp.id_empresa = $id_empresa";
        }
    
       // Filtro por valor a pagar
    
       if (!empty($_GET['filtro_valor_condicao']) && !empty($_GET['filtro_valor'])) {
        $valor = $_GET['filtro_valor'];
        $condicao = $_GET['filtro_valor_condicao'];
        switch ($condicao) {
            case 'maior':
                $where_clause[] = "cp.valor > $valor";
                break;
                case 'menor':
                    $where_clause[] = "cp.valor < $valor";
                    break;
                    case 'igual':
                        $where_clause[] = "cp.valor = $valor";
                        break;
                    }
                }
                
                // Filtro por data de pagamento
    
                if (!empty($_GET['filtro_data'])) {
                    $data_pagar = $_GET['filtro_data'];
                    $where_clause[] = "cp.data_pagar = '$data_pagar'";
                }
                if (!empty($where_clause)) {
                    $query_contas .= " WHERE " . implode(" AND ", $where_clause);
                }
            }
  
            $result_contas = $conexao->query($query_contas);
            
            
            if ($result_contas->num_rows > 0) {
                while ($row = $result_contas->fetch_assoc()){
                    $valor_formatado = 'R$ ' . number_format($row["valor"], 2, ',', '.');
                    if ($row["pago"] == 1 && !empty($row["data_pagamento"])) {
                        $data_pagamento = date('d/m/Y', strtotime($row["data_pagamento"]));
                        $data_pagar = date('d/m/Y', strtotime($row["data_pagar"]));
                    } else {
                        $data_pagamento = '-';
                    }
                    
                    
                    
                    // Definindo o status com base no campo pago

                    $status = ($row["pago"] == 1) ? 'Pago' : 'A pagar';
                    echo '<tr>';
                    echo '<td>' . $row["empresa"] . '</td>';
                    echo '<td>' . $data_pagar . '</td>';
                    echo '<td>' . $valor_formatado . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '<td>' . $data_pagamento . '</td>'; // Exibe a data de pagamento efetivo
                    echo '<td>R$ ' . number_format($row["valor_pago"], 2, ',', '.') . '</td>'; // Exibe o valor pago formatado
                    echo '<td>';
                    echo '<form action="editar_conta.php" method="GET">';
                    echo '<input type="hidden" name="id_conta_pagar" value="' . $row["id_conta_pagar"] . '">';
                    echo '<button type="submit" name="editar">Editar</button>';
                    echo '</form>';
                    echo '<form action="processar_conta.php" method="POST">';
                    echo '<input type="hidden" name="id_conta_pagar" value="' . $row["id_conta_pagar"] . '">';
                    echo '<button type="submit" name="excluir">Excluir</button>';
                    echo '</form>';          
                    if ($row["pago"] == 0) {
                        echo '<form action="processar_conta.php" method="POST">';
                        echo '<input type="hidden" name="id_conta_pagar" value="' . $row["id_conta_pagar"] . '">';
                        echo '<button type="submit" name="marcar_pago">Marcar como Pago</button>';
                        echo '</form>';
                    }
                    
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo "<tr><td colspan='5'>Não há contas a pagar cadastradas.</td></tr>";
            }
            
            $conexao->close();
            ?>
            </tbody>
            </table>
            </body>
            </html>