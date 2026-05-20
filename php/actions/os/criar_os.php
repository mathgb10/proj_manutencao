<?php
session_start();
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

// Como mudamos para FormData no frontend, usamos $_POST
$descricao    = trim($_POST['descricao'] ?? '');
$tipo         = trim($_POST['tipo'] ?? '');
$patrimonio   = trim($_POST['patrimonio'] ?? '');
$solicitante_id   = $_SESSION['user_id'] ?? 0;
$solicitante_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

if (empty($descricao) || empty($tipo)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

// Buscar automaticamente o primeiro GESTOR (ou ADMIN) disponível
$sqlGestor = "SELECT id, nome FROM usuarios WHERE permissao IN ('GESTOR','ADMIN') ORDER BY id ASC LIMIT 1";
$resGestor = $conn->query($sqlGestor);

if (!$resGestor || $resGestor->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Nenhum gestor cadastrado no sistema.']);
    exit;
}

$gestor = $resGestor->fetch_assoc();
$responsavel_id   = $gestor['id'];
$responsavel_nome = $gestor['nome'];

$conn->begin_transaction();

try {
    $sql = "INSERT INTO ordens_servico (descricao, tipo, patrimonio, status, solicitante_id, responsavel_id)
            VALUES (?, ?, ?, 'Em Aberto', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $descricao, $tipo, $patrimonio, $solicitante_id, $responsavel_id);
    $stmt->execute();
    $os_id = $stmt->insert_id;

    // Processar Anexo se houver
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['anexo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $allowed)) {
            $diretorio = "../../../uploads/anexos_os/";
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            $novo_nome = "os_" . $os_id . "_" . time() . "." . $ext;
            $destino = $diretorio . $novo_nome;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $sqlAnexo = "INSERT INTO os_anexos (os_id, nome_arquivo, caminho) VALUES (?, ?, ?)";
                $stmtA = $conn->prepare($sqlAnexo);
                $caminho_db = "../../uploads/anexos_os/" . $novo_nome;
                $stmtA->bind_param("iss", $os_id, $file['name'], $caminho_db);
                $stmtA->execute();
            }
        }
    }

    // Histórico
    $desc_hist  = "O.S. criada por $solicitante_nome e encaminhada automaticamente para o gestor $responsavel_nome.";
    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, 'OS Criada', ?, ?, ?)";
    $stmtH = $conn->prepare($sqlHist);
    $stmtH->bind_param("iiis", $os_id, $solicitante_id, $responsavel_id, $desc_hist);
    $stmtH->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Ordem de Serviço criada com sucesso!', 'os_id' => $os_id]);

} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Erro ao criar O.S.: ' . $e->getMessage()]);
}
