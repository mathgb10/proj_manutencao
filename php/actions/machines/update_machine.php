<?php
session_start();
require_once '../../configs/conexao.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aceita tanto 'id_maquina' quanto 'id' para compatibilidade com formulários
    $id = isset($_POST['id_maquina']) ? intval($_POST['id_maquina']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

    $denominacao = mysqli_real_escape_string($conn, $_POST['denominacao']);
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $numero_identificacao = mysqli_real_escape_string($conn, $_POST['numero_identificacao']);
    $numero_serie = mysqli_real_escape_string($conn, $_POST['numero_serie']);
    $ano_fabricacao = isset($_POST['ano_fabricacao']) && $_POST['ano_fabricacao'] !== '' ? intval($_POST['ano_fabricacao']) : null;
    $setor = mysqli_real_escape_string($conn, $_POST['setor']);

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        $sql = "UPDATE maquinas SET
                    denominacao = ?,
                    marca = ?,
                    modelo = ?,
                    numero_identificacao = ?,
                    numero_serie = ?,
                    ano_fabricacao = ?,
                    setor = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar atualização da máquina: " . mysqli_error($conn));
        }

        $stmt->bind_param("sssssisi", $denominacao, $marca, $modelo, $numero_identificacao, $numero_serie, $ano_fabricacao, $setor, $id);
        $stmt->execute();

        // Atualizar Checklist (Logica inteligente para manter histórico)

        // 1. Buscar IDs existentes
        $existing_ids = [];
        $sql_get_ids = "SELECT id FROM checklist_itens WHERE maquina_id = ?";
        $stmt_get_ids = mysqli_prepare($conn, $sql_get_ids);
        $stmt_get_ids->bind_param("i", $id);
        $stmt_get_ids->execute();
        $res_ids = $stmt_get_ids->get_result();
        while ($row = $res_ids->fetch_assoc()) {
            $existing_ids[] = $row['id'];
        }

        $submitted_ids = [];

        if (isset($_POST['atividade']) && isset($_POST['freq']) && is_array($_POST['atividade'])) {
            $atividades = $_POST['atividade'];
            $frequencias = $_POST['freq'];
            $ids_items = $_POST['id_item'] ?? [];

            // Preparar statements
            $sql_update = "UPDATE checklist_itens SET item_verificacao = ?, frequencia = ? WHERE id = ? AND maquina_id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);

            $sql_insert = "INSERT INTO checklist_itens (maquina_id, item_verificacao, frequencia) VALUES (?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);

            foreach ($atividades as $index => $atividade) {
                $atividade_limpa = mysqli_real_escape_string($conn, $atividade);
                $frequencia_limpa = mysqli_real_escape_string($conn, $frequencias[$index] ?? '');
                $item_id = isset($ids_items[$index]) && $ids_items[$index] !== '' ? intval($ids_items[$index]) : null;

                if (!empty($atividade_limpa)) {
                    if ($item_id && in_array($item_id, $existing_ids)) {
                        // Atualizar existente
                        $stmt_update->bind_param("ssii", $atividade_limpa, $frequencia_limpa, $item_id, $id);
                        $stmt_update->execute();
                        $submitted_ids[] = $item_id;
                    } else {
                        // Inserir novo
                        $stmt_insert->bind_param("iss", $id, $atividade_limpa, $frequencia_limpa);
                        $stmt_insert->execute();
                    }
                }
            }
        }

        // 3. Deletar itens removidos
        $ids_to_delete = array_diff($existing_ids, $submitted_ids);
        if (!empty($ids_to_delete)) {
            // Sanitizar IDs para integer
            $ids_to_delete = array_map('intval', $ids_to_delete);
            $ids_str = implode(',', $ids_to_delete);
            $sql_delete = "DELETE FROM checklist_itens WHERE id IN ($ids_str)";
            mysqli_query($conn, $sql_delete);
        }

        mysqli_commit($conn);
        $_SESSION['success_message'] = "Máquina e checklist atualizados com sucesso.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Erro ao atualizar: " . $e->getMessage();
    }

    header("Location: ../../views/maquinas.php");
    exit();
}
