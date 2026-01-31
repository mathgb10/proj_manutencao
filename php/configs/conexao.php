<?php
    $sv = "localhost";
    $user = "root";
    $pass = "";
    $db = "manutencao_tds26";
    $port = 3308;

    // Tente Conectar sem Porta definida, Se não der Entre pela Porta definida.
    try {
        $conn = mysqli_connect($sv,$user,$pass,$db);
    } catch (mysqli_sql_exception $e) {
        $conn = mysqli_connect($sv,$user,$pass,$db,$port);
    }
    // Fiz pq no do Senai precisa mas em my house não precisa. OBS: TIRA ESSE COMENTARIO NA HORA DE APRESENTAR
?>