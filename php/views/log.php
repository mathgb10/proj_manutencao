<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php';

// --- VERIFICAÇÃO DE SEGURANÇA (Evita o erro Fatal) ---
if (!isset($conn)) {
    // Tenta usar $mysqli se $conn não existir, ou para o script
    if (isset($mysqli)) {
        $conn = $mysqli;
    } else {
        die("<h3>Erro Crítico:</h3> A conexão com o banco de dados falhou. <br>Verifique se a variável no arquivo <b>conexao.php</b> se chama <b>\$conn</b>.");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Logs - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="div-btns-pages">
            <form action="" method="GET" class="form-pesquisa">
                <div class="search-container">
                    <?php
                    $busca_atual = isset($_GET['search']) ? $_GET['search'] : '';
                    ?>
                    <div class="box-pesquisa">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa"
                            value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar..."
                            class="input-pesquisa">
                        <?php if ($busca_atual): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn-clear-search"><i
                                    class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                    </div>
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-file-earmark-code"></i>
                <h2>Logs do Sistema</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Endereço de IP</th>
                        <th>Comando SQL</th>
                        <th>Data/Hora</th>
                    </thead>
                    <tbody id="tabela-logs">
                        <?php
                        // --- LÓGICA DE PESQUISA AVANÇADA COM NOME ---
                        
                        // SQL Base: Traz os logs e junta com a tabela de usuários para pegar o nome
                        // Usamos 'usuarios.nome AS nome_real' para não confundir
                        $sql_base = "SELECT logs.*, usuarios.nome AS nome_real 
                                 FROM logs 
                                 LEFT JOIN usuarios ON logs.usuario_id = usuarios.id";

                        if (!empty($busca_atual)) {
                            $termo_seguro = mysqli_real_escape_string($conn, $busca_atual);

                            // Filtra pelo Nome do usuário (da tabela usuarios) ou dados do log
                            $sql = $sql_base . " WHERE 
                                usuarios.nome LIKE '%$termo_seguro%' OR 
                                logs.ip_address LIKE '%$termo_seguro%' OR 
                                logs.sql_command LIKE '%$termo_seguro%'";
                        } else {
                            $sql = $sql_base;
                        }

                        // Ordena do mais recente para o mais antigo
                        $sql .= " ORDER BY logs.id DESC";

                        // Executa a query
                        $resultado = $conn->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {

                                // Verifica se encontrou o nome (se o usuário não foi excluído)
                                $nome_exibicao = !empty($linha["nome_real"]) ? htmlspecialchars($linha["nome_real"]) : "<span style='color: #ff6b6b; font-size: 0.9em;'>Ex-Usuário (ID: " . $linha['usuario_id'] . ")</span>";

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($linha["id"]) . "</td>";
                                echo "<td>" . $nome_exibicao . "</td>";
                                echo "<td>" . htmlspecialchars($linha["ip_address"]) . "</td>";
                                // Limita o tamanho do comando SQL visualmente se for muito grande
                                echo "<td title='" . htmlspecialchars($linha["sql_command"]) . "'>" . substr(htmlspecialchars($linha["sql_command"]), 0, 50) . (strlen($linha["sql_command"]) > 50 ? '...' : '') . "</td>";
                                echo "<td>" . date("d/m/Y H:i", strtotime($linha["data_hora"])) . "</td>"; // Formata a data BR
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:20px; color: #888;'>Nenhum registro encontrado.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="div-btns-change">
                <button id="btn-ant" type="button"><i class="bi bi-chevron-left"></i></button>

                <button id="btn-prox" type="button"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>
