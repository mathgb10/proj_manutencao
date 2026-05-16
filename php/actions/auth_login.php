<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require __DIR__ . '/../configs/conexao.php';

    $email = 'ola@gmail.com';
    $senha = '123456';

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
            $_SESSION['user_foto'] = $infos_db['foto'];


            header("Location: ../views/dashboard.php");
            exit;
        } else {
            header("Location: ../../index.php?erro=pass");
            exit;
        }
    } else {
        header("Location: ../../index.php?erro=email");
        exit;
    }
    // qnd o banco tiver ok nois testa, por enquanto eu vo pedir pro se pq se vai dar conta ai dos seus jeitos
    // deixa a navbar responsiva
}

