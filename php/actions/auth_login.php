<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require '../configs/conexao.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];   

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
            $_SESSION['user_senha_padrao'] = $infos_db['senha_padrao'];


            header("Location: ../views/dashboard.php");
        } else {
            header("Location: ../../index.php?erro=pass");
        }
    } else {
        header("Location: ../../index.php?erro=email");
    }
    // qnd o banco tiver ok nois testa, por enquanto eu vo pedir pro se pq se vai dar conta ai dos seus jeitos
    // deixa a navbar responsiva
}
