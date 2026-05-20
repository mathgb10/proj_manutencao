<?php
session_start();
$_SESSION['user_id'] = 1; // fake login
$_SESSION['user_permissao'] = 'ADMIN';

// simulate the GET request
$_GET['aba'] = 'todas';

ob_start();
include 'c:/xampp/htdocs/senai/manutencao/php/actions/os/listar_os.php';
$output = ob_get_clean();

echo $output;
