<?php
$sv = "localhost";
$user = "root";
$pass = "";
$db = "manutencao_tds2026";
$port = 3308;

// Tente Conectar sem Porta definida, Se não der Entre pela Porta definida.

$conn = mysqli_connect($sv, $user, $pass, $db, $port);

// Conexão com o banco de dados do projeto NR12
$db_nr12 = "nr12";
$conn_nr12 = mysqli_connect($sv, $user, $pass, $db_nr12, $port);

if (!$conn_nr12) {
    // Silencioso ou logar erro
}

// Fiz pq no do Senai precisa mas em my house não precisa. OBS: TIRA ESSE COMENTARIO NA HORA DE APRESENTAR

function salvarLog($conn, $sql_command)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $usuario_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Sanitizar o comando SQL para evitar injeção SQL no log, embora prepared statements sejam melhores.
    // Como é para log, vamos apenas escapar strings simples ou usar prepared statement para o próprio insert do log.

    $query_log = "INSERT INTO logs (usuario_id, ip_address, sql_command) VALUES (?, ?, ?)";
    $stmt_log = mysqli_prepare($conn, $query_log);

    // Se usuario_id for null, bind_param precisa saber. Mas bind_param não aceita null direto em 'i' se strict.
    // Vamos garantir que seja tratado.

    if ($stmt_log) {
        $stmt_log->bind_param("iss", $usuario_id, $ip_address, $sql_command);
        $stmt_log->execute();
        $stmt_log->close();
    }
}
