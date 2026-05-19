<?php
session_start();
require_once __DIR__ . '/../../configs/conexao.php';

$response = array('success' => false, 'message' => 'Erro desconhecido.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {

        $maquinaId = intval($_POST['id']);

        $sql = "DELETE FROM maquinas WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            $stmt->bind_param("i", $maquinaId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    // Registra no log que a máquina foi deletada
                    if (function_exists('salvarLog')) {
                        salvarLog($conn, "DELETE FROM maquinas WHERE id = $maquinaId");
                    }
                    $response['success'] = true;
                    $response['message'] = 'Máquina deletada com sucesso.';
                } else {
                    $response['message'] = 'Nenhuma máquina encontrada com o ID fornecido.';
                }
            } else {
                $response['message'] = 'Erro ao executar a consulta: ' . $stmt->error;
            }
        } else {
            $response['message'] = 'Erro ao preparar a consulta: ' . mysqli_error($conn);
        }
    } else {
        $response['message'] = 'ID da máquina não fornecido.';
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
