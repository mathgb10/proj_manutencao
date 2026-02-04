<?php
require "../../configs/conexao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $query = "DELETE FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            salvarLog($conn, "DELETE FROM usuarios WHERE id = $id");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar usuário.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID não fornecido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
