<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require '../configs/conexao.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $lembrar = isset($_POST['rememberMe']) ? $_POST['rememberMe'] : '';

    $coletando_db = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $coletando_db);
    $stmt->bind_param("s", $email);
    $stmt->execute();   
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $infos_db = mysqli_fetch_assoc($resultado);
        $senha_db = $infos_db['senha'];

        if (password_verify($senha, $senha_db)) {
            session_start();
            $_SESSION['user_id'] = $infos_db['id'];
            $_SESSION['user_nome'] = $infos_db['nome'];
            $_SESSION['user_permissao'] = $infos_db['permissao'];

            header("Location: ../views/dashboard.php");
        } else {
            header("Location: ../../index.php?erro=pass");
        }
    } else {
        header("Location: ../../index.php?erro=email");
    }

    if ($lembrar === 'checked') {
        $gerar_token = bin2hex(random_bytes(16));

        $atualizar_token = "UPDATE usuarios SET token = ? WHERE email = ?";
        $stmt_token = mysqli_prepare($conn, $atualizar_token);
        $stmt_token->bind_param("ss", $gerar_token, $email);
        $stmt_token->execute();
        $stmt_token->get_result();
        setcookie("rememberMe", $gerar_token, time() + (86400 * 30), "/");
        
    }
    // qnd o banco tiver ok nois testa, por enquanto eu vo pedir pro se pq se vai dar conta ai dos seus jeitos
    // deixa a navbar responsiva
}
