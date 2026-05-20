<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_permissao'] = 'NORMAL';
$_GET['aba'] = 'todas';
$_GET['search'] = 'test';

ob_start();
include 'c:/xampp/htdocs/senai/manutencao/php/actions/os/listar_os.php';
$output = ob_get_clean();

echo $output;
