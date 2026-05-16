<?php
session_start();
require_once("../../configs/conexao.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$denominacao = mysqli_real_escape_string($conn, $_POST['denominacao'] ?? '');
$marca = mysqli_real_escape_string($conn, $_POST['marca'] ?? '');
$modelo = mysqli_real_escape_string($conn, $_POST['modelo'] ?? '');
$numero_identificacao = mysqli_real_escape_string($conn, $_POST['numero_identificacao'] ?? '');
$tipomaquina_id = isset($_POST['tipomaquina_id']) && $_POST['tipomaquina_id'] !== 'semValor' ? intval($_POST['tipomaquina_id']) : null;
$ano_fabricacao = mysqli_real_escape_string($conn, $_POST['ano_fabricacao'] ?? '');
$setor = mysqli_real_escape_string($conn, $_POST['setor'] ?? '');

// echo($denominacao);
// echo($marca);
// echo($modelo);
// echo($numero_identificacao);
// echo($ano_fabricacao);
// echo($ano_fabricacao);
// echo($setor);


// Validação básica
if (empty($denominacao) || empty($numero_identificacao)) {
    // echo "<script>alert('Por favor, preencha os campos obrigatórios (Denominação e Nº Identificação).'); window.history.back();</script>";
    // exit();
}

$sql = "INSERT INTO maquinas (denominacao, marca, modelo, numero_identificacao, tipomaquina_id, ano_fabricacao, setor) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    $stmt->bind_param("ssssiis", $denominacao, $marca, $modelo, $numero_identificacao, $tipomaquina_id, $ano_fabricacao, $setor);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $maquina_id = mysqli_insert_id($conn);

        // Processar Checklist
        if (isset($_POST['atividade']) && isset($_POST['freq']) && is_array($_POST['atividade'])) {
            $atividades = $_POST['atividade'];
            $frequencias = $_POST['freq'];

            $sql_checklist = "INSERT INTO checklist_itens (maquina_id, item_verificacao, frequencia) VALUES (?, ?, ?)";
            $stmt_checklist = mysqli_prepare($conn, $sql_checklist);

            if ($stmt_checklist) {
                foreach ($atividades as $index => $atividade) {
                    $atividade_limpa = mysqli_real_escape_string($conn, $atividade);
                    $frequencia_limpa = mysqli_real_escape_string($conn, $frequencias[$index] ?? ''); // Fallback seguro

                    if (!empty($atividade_limpa)) {
                        $stmt_checklist->bind_param("iss", $maquina_id, $atividade_limpa, $frequencia_limpa);
                        $stmt_checklist->execute();
                    }
                }
                $stmt_checklist->close();
            }
        }

        echo "<script>window.location.href='../../views/maquinas.php?sucesso=Máquina e checklist cadastrados';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar a máquina. Por favor, tente novamente.'); window.location.href='../../views/maquinas.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Erro na preparação da consulta: " . mysqli_error($conn) . "'); window.location.href='../../views/maquinas.php';</script>";
}
?>
