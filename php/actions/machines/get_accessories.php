<?php
require __DIR__ . '/../../configs/conexao.php';

if (isset($_GET['id'])) {
    $id_maquina = $_GET['id'];

    // Buscar Informações da Máquina
    $sql_maquina = "SELECT denominacao, modelo FROM maquinas WHERE id = ?";
    $stmt_maquina = $conn->prepare($sql_maquina);
    $stmt_maquina->bind_param("i", $id_maquina);
    $stmt_maquina->execute();
    $result_maquina = $stmt_maquina->get_result();
    $maquina = $result_maquina->fetch_assoc();

    // Buscar Acessórios
    $sql_acessorios = "SELECT * FROM acessorios WHERE maquina_id = ?";
    $stmt_acessorios = $conn->prepare($sql_acessorios);
    $stmt_acessorios->bind_param("i", $id_maquina);
    $stmt_acessorios->execute();
    $result_acessorios = $stmt_acessorios->get_result();

    $acessorios = [];
    while ($row = $result_acessorios->fetch_assoc()) {
        $acessorios[] = $row;
    }

    echo json_encode([
        'success' => true,
        'maquina' => $maquina,
        'acessorios' => $acessorios
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'ID da máquina não fornecido']);
}
?>
