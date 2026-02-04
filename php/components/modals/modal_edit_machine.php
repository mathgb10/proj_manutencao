<?php

require_once '../configs/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "SELECT * FROM maquinas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $maquina = $result->fetch_assoc();
    } else {
        echo "Máquina não encontrada.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Máquina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="modal-fundo" id="editarMaquina">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Editar Máquina</h4>
                        </div>

                        <div class="card-body">
                            <form action="../actions/machines/update_machines.php" method="POST">

                                <input type="hidden" name="id" value="<?php echo $maquina['id']; ?>">

                                <div class="mb-3">
                                    <label for="denominacao" class="form-label fw-bold">Denominação *</label>
                                    <input type="text" class="form-control" name="denominacao" id="denominacao"
                                        required
                                        value="<?php echo $maquina['denominacao']; ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control" name="marca" id="marca"
                                            value="<?php echo $maquina['marca']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="modelo" class="form-label">Modelo</label>
                                        <input type="text" class="form-control" name="modelo" id="modelo"
                                            value="<?php echo $maquina['modelo']; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_identificacao" class="form-label">Nº Identificação (Patrimônio)</label>
                                        <input type="text" class="form-control" name="numero_identificacao" id="numero_identificacao"
                                            value="<?php echo $maquina['numero_identificacao']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_serie" class="form-label">Nº de Série</label>
                                        <input type="text" class="form-control" name="numero_serie" id="numero_serie"
                                            value="<?php echo $maquina['numero_serie']; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ano_fabricacao" class="form-label">Ano Fabricação</label>
                                        <input type="number" class="form-control" name="ano_fabricacao" id="ano_fabricacao"
                                            min="1900" max="2100"
                                            value="<?php echo $maquina['ano_fabricacao']; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="setor" class="form-label">Setor</label>
                                        <input type="text" class="form-control" name="setor" id="setor"
                                            value="<?php echo $maquina['setor']; ?>">
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="lista_maquinas.php" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


</body>

</html>