<?php
session_start();

// Se essa váriavel de Sessão não existir significa que o cara não está logado.
// Então mando ele de volta pra tela de login com um aviso de erro.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php?acesso=negado");
} else {
    $id_usuario = $_SESSION['user_id'];
    $nome_usuario = $_SESSION['user_nome'] ?? $_SESSION['colaborador_nome'] ?? 'Usuário';
    $permissao_usuario = $_SESSION['user_permissao'] ?? $_SESSION['colaborador_permissao'] ?? 'NORMAL';

    $atualmente_em = basename($_SERVER['PHP_SELF']);

    // Bloqueio o acesso a tela de controle de usuários se o nível de permissão não for ADMIN
    if ($atualmente_em == 'usuarios.php') {
        if ($permissao_usuario != "ADMIN") {
            header("Location: dashboard.php?acesso=negado");
        }
    }
}
