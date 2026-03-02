<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../configs/conexao.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">

</head>

<body>
    <?php require '../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require '../components/header.php'; ?>

        <div class="div-btns-pages">
            <form action="" method="GET" class="form-pesquisa">
                <div class="search-container">
                    <?php
                    $busca_atual = isset($_GET['search']) ? $_GET['search'] : '';
                    ?>
                    <div class="box-pesquisa">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa" value="<?php echo htmlspecialchars($busca_atual); ?>"
                            placeholder="Pesquisar..." class="input-pesquisa">
                        <?php if ($busca_atual): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn-clear-search"><i class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                    </div>
                    <!-- Hidden submit button to allow Enter to search -->
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>
            <button class="btn" onclick="showModal('adicaoUser')">Adicionar Usuário <i
                    class="bi bi-person-add"></i>
            </button>
        </div>

        <div class="tabela-bg2">
            <table class="tabela-main">
                <thead>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Senha</th>
                    <th>Permissão</th>
                    <th>Ações</th>
                </thead>
                <tbody id="tabela-usuarios">
                    <?php
                    // --- LÓGICA DE PESQUISA ---

                    if (!empty($busca_atual)) {
                        // Limpa o termo para segurança do Banco de Dados
                        $termo_seguro = $conn->real_escape_string($busca_atual);

                        // SQL filtrando por Nome, Email ou Permissão
                        $sql = "SELECT * FROM usuarios WHERE 
                                nome LIKE '%$termo_seguro%' OR 
                                email LIKE '%$termo_seguro%' OR 
                                permissao LIKE '%$termo_seguro%'";
                    } else {
                        // Se não tiver pesquisa, traz todos os usuários
                        $sql = "SELECT * FROM usuarios";
                    }

                    $resultado = $conn->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while ($linha = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $linha["nome"] . "</td>";
                            echo "<td>" . $linha["email"] . "</td>";
                            echo "<td>******</td>"; // Senha oculta
                            echo "<td> <div class='div-permissao'>" . $linha["permissao"] . "</div></td>";

                            // Botões de Ação
                            echo "<td>
                                    <div>
                                        <button class='btnAcao editar' type='button' onclick=\"showModal('edicaoUser', " . $linha['id'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' style='background-color: #ffc107; color: #000;' title='Resetar Senha' onclick=\"showModal('resetPass', " . $linha['id'] . ")\"><i class='bi bi-key-fill'></i></button>
                                        <button class='btnAcao deletar' type='button' onclick=\"showModal('dell', " . $linha['id'] . ",'usuario')\"><i class='bi bi-trash'></i></button>
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:15px;'>Nenhum usuário encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="div-btns-change">
            <button id="btn-ant" type="button"><i class="bi bi-chevron-left"></i></button>

            <button id="btn-prox" type="button"><i class="bi bi-chevron-right"></i></button>
        </div>

    </section>

    <script>
        // Tratamento de mensagens de sucesso/erro
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('msg')) {
                const msg = urlParams.get('msg');
                if (msg === 'senha_alterada') {
                    alert('✓ Senha do usuário alterada com sucesso!');
                } else if (msg === 'senha_resetada') {
                    alert('Senha alterada para senaisp por padrão.');
                }
                // Remove o parâmetro da URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            if (urlParams.has('erro')) {
                const erro = urlParams.get('erro');
                if (erro === 'senhas_nao_coincidem') {
                    alert('✗ As senhas informadas não coincidem!');
                } else if (erro === 'sem_permissao') {
                    alert('✗ Você não tem permissão para realizar esta ação!');
                } else if (erro === 'erro_banco') {
                    alert('✗ Erro ao processar a requisição no banco de dados!');
                }
                // Remove o parâmetro da URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>