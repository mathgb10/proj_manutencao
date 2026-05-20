<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

$os_id               = 0;
$novo_responsavel_id = 0;
$motivo              = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $os_id               = intval($_POST['os_id'] ?? 0);
        $novo_responsavel_id = intval($_POST['responsavel_id'] ?? 0);
        $motivo              = trim($_POST['motivo'] ?? '');
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $os_id               = intval($input['os_id'] ?? 0);
            $novo_responsavel_id = intval($input['responsavel_id'] ?? 0);
            $motivo              = trim($input['motivo'] ?? '');
        }
    }
}

$usuario_id          = $_SESSION['user_id'] ?? 0;
$usuario_nome        = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0 || $novo_responsavel_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos. Informe ID da O.S. e novo responsável']);
    exit;
}

if (empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Informe o motivo do encaminhamento']);
    exit;
}

// Buscar OS atual
$sqlOS = "SELECT os.*, resp.nome AS responsavel_nome FROM ordens_servico os
          LEFT JOIN usuarios resp ON os.responsavel_id = resp.id
          WHERE os.id = ?";
$stmtOS = $conn->prepare($sqlOS);
$stmtOS->bind_param("i", $os_id);
$stmtOS->execute();
$resOS = $stmtOS->get_result();

if ($resOS->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'O.S. não encontrada']);
    exit;
}

$os = $resOS->fetch_assoc();

// Apenas o responsável atual pode encaminhar (ou ADMIN/GESTOR)
$permissao = $_SESSION['user_permissao'] ?? 'NORMAL';
if ($os['responsavel_id'] != $usuario_id && $permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
    echo json_encode(['success' => false, 'message' => 'Apenas o responsável atual pode encaminhar esta O.S.']);
    exit;
}

// Buscar nome do novo responsável
$sqlResp = "SELECT nome FROM usuarios WHERE id = ?";
$stmtResp = $conn->prepare($sqlResp);
$stmtResp->bind_param("i", $novo_responsavel_id);
$stmtResp->execute();
$resResp = $stmtResp->get_result();

if ($resResp->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Responsável destino não encontrado']);
    exit;
}

$novo_resp_nome = $resResp->fetch_assoc()['nome'];

// Iniciar Transação
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

    // Atualizar OS
    $sqlUpdate = "UPDATE ordens_servico SET status = 'Aguardando Aprovação', responsavel_id = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ii", $novo_responsavel_id, $os_id);
    $stmtUpdate->execute();

    $desc_hist  = "O.S. encaminhada por $usuario_nome para $novo_resp_nome em " . date('d/m/Y H:i') . ". Motivo: $motivo";
    if ($anexo_dados) {
        $desc_hist .= "\n\n[Anexo adicionado: " . $anexo_dados['nome'] . "]";
    }
    $status_hist = "OS Encaminhada";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    $stmtHist->bind_param("isiis", $os_id, $status_hist, $usuario_id, $novo_responsavel_id, $desc_hist);
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
    echo json_encode(['success' => true, 'message' => "O.S. encaminhada para $novo_resp_nome com sucesso!"]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao encaminhar O.S.: ' . $e->getMessage()]);
}
?>
