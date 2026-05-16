<?php
session_start();
require_once __DIR__ . "/../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $id_usuario = $_SESSION['user_id'];
    $arquivo = $_FILES['foto'];
    
    // Validações básicas
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $extensoes_permitidas)) {
        header("Location: ../views/perfil.php?erro=extensao");
        exit;
    }
    
    if ($arquivo['size'] > 2 * 1024 * 1024) { // 2MB
        header("Location: ../views/perfil.php?erro=tamanho");
        exit;
    }
    
    $novo_nome = "perfil_" . $id_usuario . "_" . time() . "." . $extensao;
    $destino = "../../uploads/perfis/" . $novo_nome;
    
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        // Deletar foto antiga se existir
        if (!empty($_SESSION['user_foto'])) {
            $antiga = "../../uploads/perfis/" . $_SESSION['user_foto'];
            if (file_exists($antiga)) unlink($antiga);
        }
        
        // Atualizar banco
        $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $id_usuario);
        
        if ($stmt->execute()) {
            $_SESSION['user_foto'] = $novo_nome;
            header("Location: ../views/perfil.php?sucesso=upload");
        } else {
            header("Location: ../views/perfil.php?erro=banco");
        }
    } else {
        header("Location: ../views/perfil.php?erro=upload");
    }
} else {
    header("Location: ../views/perfil.php");
}
