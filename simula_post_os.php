<?php
// simula_post_os.php
session_start();
$_SESSION['user_id'] = 1; // ID de teste
$_SESSION['user_nome'] = 'Admin Teste';

$url = 'http://localhost/nr12/manutencaoMath/php/actions/os/criar_os.php';
$data = [
    'descricao' => 'Teste de criação via script',
    'tipo' => 'Manutenção',
    'responsavel_id' => 1
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP CODE: $httpCode\n";
echo "RESPONSE:\n$response\n";

if ($response === false) {
    echo "CURL ERROR: " . curl_error($ch);
}

curl_close($ch);
?>
