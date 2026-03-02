<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../configs/conexao.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow de Manutenção - SENAI</title>

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
            <button class="btn" onclick="showModal('')">Abrir Nova O.S <i class="bi bi-file-earmark-text"></i></button>

        </div>

        <div class="card-table">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Nº O.S.</th>
                        <th style="width: 25%;">EQUIPAMENTO</th>
                        <th style="width: 15%;">SOLICITANTE</th>
                        <th style="width: 20%;">TÉCNICO / GESTOR</th>
                        <th style="width: 15%;">STATUS</th>
                        <th style="width: 15%; text-align: center;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">#2026-001</td>
                        <td class="col-info">
                            <strong>TORNO ROMI T240</strong>
                            <span>NI: 1052694</span>
                        </td>
                        <td>João Func.</td>
                        <td><span class="aguardando-text">Aguardando...</span></td>
                        <td><span class="status-badge status-solicitada">SOLICITADA</span></td>
                        <td style="text-align: center;">
                            <button class="btn-aprovar">Aprovar</button>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold;">#2026-002</td>
                        <td class="col-info">
                            <strong>FRESA UNIVERSAL</strong>
                            <span>NI: 2018552</span>
                        </td>
                        <td>Admin</td>
                        <td class="col-info">
                            <strong>Carlos Alberto</strong>
                            <span class="gestor-highlight">Gestor: Roberto M.</span>
                        </td>
                        <td><span class="status-badge status-execucao">EM EXECUÇÃO</span></td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center;">
                                <button class="btn-acao-dark">
                                    <i class="bi bi-key-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>