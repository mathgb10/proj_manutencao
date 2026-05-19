<?php
// Suprimir warnings/notices que quebram JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../configs/conexao.php';
session_start();

header('Content-Type: application/json');

$os_id      = intval($_POST['os_id'] ?? 0);
$observacao = trim($_POST['observacao'] ?? '');
$usuario_id = $_SESSION['user_id'] ?? null;

if (!$os_id || empty($observacao) || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos!']);
    exit;
}

// Buscar o responsável atual para manter no histórico
$sqlOS = "SELECT responsavel_id FROM ordens_servico WHERE id = ?";
$stmtOS = $conn->prepare($sqlOS);
$stmtOS->bind_param("i", $os_id);
$stmtOS->execute();
$resOS = $stmtOS->get_result();
$os = $resOS->fetch_assoc();

if (!$os) {
    echo json_encode(['success' => false, 'message' => 'O.S. não encontrada!']);
    exit;
}

$responsavel_id = $os['responsavel_id'];

$conn->begin_transaction();

try {
    // Processar Anexo preliminarmente se houver
    $anexo_dados = null;
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['anexo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $allowed)) {
            $diretorio = "../../../uploads/anexos_os/";
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            $novo_nome = "os_" . $os_id . "_" . time() . "_" . uniqid() . "." . $ext;
            $destino = $diretorio . $novo_nome;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $caminho_db = "../../uploads/anexos_os/" . $novo_nome;
                $anexo_dados = [
                    'nome' => $file['name'],
                    'caminho' => $caminho_db
                ];
            }
        }
    }

    // Se houver anexo, adicionar nota ao histórico
    $descricao_completa = $observacao;
    if ($anexo_dados) {
        $descricao_completa .= "\n\n[Anexo adicionado: " . $anexo_dados['nome'] . "]";
    }

    // Inserir no histórico como Observação
    $sqlHist = "INSERT INTO os_historico (os_id, origem_id, destino_id, status, descricao) 
                VALUES (?, ?, ?, 'Observação', ?)";
    $stmtHist = $conn->prepare($sqlHist);
    $stmtHist->bind_param("iiis", $os_id, $usuario_id, $responsavel_id, $descricao_completa);
    $stmtHist->execute();
    
    $historico_id = $stmtHist->insert_id;

    // Gravar anexo no banco se houver e estiver associado
    if ($anexo_dados && $historico_id) {
        $sqlAnexo = "INSERT INTO os_anexos (os_id, historico_id, nome_arquivo, caminho) VALUES (?, ?, ?, ?)";
        $stmtA = $conn->prepare($sqlAnexo);
        $stmtA->bind_param("iiss", $os_id, $historico_id, $anexo_dados['nome'], $anexo_dados['caminho']);
        $stmtA->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Observação adicionada com sucesso!']);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar observação: ' . $e->getMessage()]);
}
?>
