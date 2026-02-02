<?php
// 1. OBRIGATÓRIO: Iniciar a sessão para poder ler e destruir ela
session_start();

require_once '../configs/conexao.php';

// Verifica se tem alguém logado antes de tentar limpar o banco
if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];

    // Limpa o token no banco (Segurança)
    $sql = "UPDATE usuarios SET token = NULL WHERE id = ?";
    
    // ATENÇÃO: Verifique se no seu conexao.php a variável é $conn ou $conexao
    // Estou usando $conn seguindo seu código anterior
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// 2. Destrói a sessão
session_unset();
session_destroy();

// 3. Destrói o Cookie
if (isset($_COOKIE['rememberMe'])) {
    // CORREÇÃO: Removi o "name:", deixei apenas os valores
    setcookie('rememberMe', '', time() - 3600, '/');
    unset($_COOKIE['rememberMe']);
}

// 4. Redireciona e encerra
header("Location: ../../index.php");
exit;
?>