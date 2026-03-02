<?php
require '../../configs/conexao.php';

// Recebe os dados JSON do corpo da requisição
$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['id_maquina']) && isset($dados['acessorios'])) {
    $id_maquina = $dados['id_maquina'];
    $acessorios = $dados['acessorios'];

    $conn->begin_transaction();

    try {
        // Primeiro, remove todos os acessórios existentes para esta máquina (estratégia simples: delete-all-insert-all)
        // Ou melhor, deletar apenas os que não estão na lista recebida, mas delete-all é mais fácil para garantir consistência se IDs não forem enviados
        // Como a modal envia tudo de novo, podemos limpar e recriar.

        $delete_sql = "DELETE FROM acessorios WHERE maquina_id = ?";
        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("i", $id_maquina);
        $stmt_delete->execute();

        // Inserir os novos acessórios
        $insert_sql = "INSERT INTO acessorios (maquina_id, denominacao, aplicacao, caracteristicas, numero_identificacao) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);

        foreach ($acessorios as $acessorio) {
            // Verificar se os campos não estão vazios
            if (!empty($acessorio['denominacao'])) {
                $stmt_insert->bind_param(
                    "issss",
                    $id_maquina,
                    $acessorio['denominacao'],
                    $acessorio['aplicacao'],
                    $acessorio['caracteristicas'],
                    $acessorio['ni']
                );
                $stmt_insert->execute();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Acessórios salvos com sucesso!']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar acessórios: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
}
?>