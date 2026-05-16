<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error_log.txt');

// Capturar erros fatais e retornar como JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erro PHP: ' . $error['message'], 'dados' => [], 'contadores' => []]);
    }
});

session_start();
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco', 'dados' => [], 'contadores' => []]);
    exit;
}

$usuario_logado_id = intval($_SESSION['user_id'] ?? 0);
$permissao         = $_SESSION['user_permissao'] ?? 'NORMAL';

$aba   = isset($_GET['aba'])    ? trim($_GET['aba'])    : 'abertas';
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 10;
$offset = ($pagina_atual - 1) * $limite;

$where  = "WHERE 1=1";
$params = [];
$types  = "";

/*
 * =============================================
 *  REGRAS DE VISIBILIDADE POR ABA
 * =============================================
 *
 * ABERTAS   â†’ status = 'Em Aberto'
 *             Todo mundo pode ver (é uma solicitação nova).
 *
 * ANDAMENTO â†’ status IN ('Aguardando Aprovação','Aceita')
 *             Apenas o responsável atual OU quem criou (solicitante) pode ver.
 *             ADMIN vê tudo.
 *
 * ARQUIVADAS â†’ status = 'Arquivada'
 *              Apenas o criador original (solicitante_id) OU quem finalizou (responsavel_id).
 *              ADMIN vê tudo.
 */

if ($aba === 'abertas') {
    $where .= " AND os.status IN ('Em Aberto', 'Aguardando Aprovação')";
    if ($permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
        $where .= " AND (os.solicitante_id = ? OR os.responsavel_id = ?)";
        $params[] = $usuario_logado_id;
        $params[] = $usuario_logado_id;
        $types .= "ii";
    }

} elseif ($aba === 'andamento') {
    $where .= " AND os.status = 'Aceita'";
    if ($permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
        $where .= " AND (os.responsavel_id = ? OR os.solicitante_id = ?)";
        $params[] = $usuario_logado_id;
        $params[] = $usuario_logado_id;
        $types .= "ii";
    }

} elseif ($aba === 'arquivadas') {
    $where .= " AND os.status = 'Arquivada'";
    if ($permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
        $where .= " AND (os.solicitante_id = ? OR os.responsavel_id = ?)";
        $params[] = $usuario_logado_id;
        $params[] = $usuario_logado_id;
        $types .= "ii";
    }

} else {
    // Fallback: abertas
    $where .= " AND os.status = 'Em Aberto'";
}

// Filtro de busca por texto (incluindo número da O.S.)
if (!empty($busca)) {
    $where .= " AND (os.id LIKE ? OR os.descricao LIKE ? OR sol.nome LIKE ? OR resp.nome LIKE ? OR os.patrimonio LIKE ?)";
    $termo    = "%$busca%";
    $params[] = $termo;
    $params[] = $termo;
    $params[] = $termo;
    $params[] = $termo;
    $params[] = $termo;
    $types   .= "sssss";
}

$sql = "SELECT
            os.id,
            os.descricao,
            os.tipo,
            os.status,
            os.patrimonio,
            os.gasto,
            os.obs_finalizacao,
            os.criado_em,
            os.atualizado_em,
            os.solicitante_id,
            os.responsavel_id,
            os.anterior_responsavel_id,
            sol.nome AS solicitante_nome,
            resp.nome AS responsavel_nome
        FROM ordens_servico os
        INNER JOIN usuarios sol  ON os.solicitante_id = sol.id
        INNER JOIN usuarios resp ON os.responsavel_id = resp.id
        $where
        ORDER BY os.criado_em DESC";

// --- Cálculo de Paginação ---
$sql_count = "SELECT COUNT(*) as total 
              FROM ordens_servico os
              INNER JOIN usuarios sol  ON os.solicitante_id = sol.id
              INNER JOIN usuarios resp ON os.responsavel_id = resp.id
              $where";
$stmt_count = $conn->prepare($sql_count);
if ($stmt_count) {
    if (!empty($params)) {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $total_registros = $stmt_count->get_result()->fetch_assoc()['total'];
    $total_paginas = ceil($total_registros / $limite);
} else {
    $total_paginas = 1;
}
if ($total_paginas == 0) $total_paginas = 1;

$sql .= " LIMIT $limite OFFSET $offset";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro SQL: ' . $conn->error, 'dados' => [], 'contadores' => []]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$ordens = [];
while ($linha = $resultado->fetch_assoc()) {
    $ordens[] = $linha;
}

// ---- Contadores para as 3 abas (respeitando visibilidade) ----
if ($permissao === 'ADMIN' || $permissao === 'GESTOR') {
    $sqlCount = "SELECT
        SUM(CASE WHEN status IN ('Em Aberto', 'Aguardando Aprovação') THEN 1 ELSE 0 END) AS abertas,
        SUM(CASE WHEN status = 'Aceita' THEN 1 ELSE 0 END) AS andamento,
        SUM(CASE WHEN status = 'Arquivada' THEN 1 ELSE 0 END) AS arquivadas
    FROM ordens_servico";
    $resCount = $conn->query($sqlCount);
} else {
    $sqlCount = "SELECT
        SUM(CASE WHEN status IN ('Em Aberto', 'Aguardando Aprovação') 
                 AND (solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS abertas,
        SUM(CASE WHEN status = 'Aceita'
                 AND (responsavel_id = $usuario_logado_id OR solicitante_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS andamento,
        SUM(CASE WHEN status = 'Arquivada'
                 AND (solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS arquivadas
    FROM ordens_servico";
    $resCount = $conn->query($sqlCount);
}

$contadores = ['abertas' => 0, 'andamento' => 0, 'arquivadas' => 0];
if ($resCount && $row = $resCount->fetch_assoc()) {
    $contadores = [
        'abertas'    => intval($row['abertas'] ?? 0),
        'andamento'  => intval($row['andamento'] ?? 0),
        'arquivadas' => intval($row['arquivadas'] ?? 0),
    ];
}

echo json_encode([
    'success'       => true,
    'dados'         => $ordens,
    'contadores'    => $contadores,
    'usuario_id'    => $usuario_logado_id,
    'permissao'     => $permissao,
    'pagina_atual'  => $pagina_atual,
    'total_paginas' => $total_paginas
]);
?>
