<?php
require_once __DIR__ . '/../../configs/conexao.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    http_response_code(500);
    echo json_encode([
        "mensagem" => "Erro interno no servidor.",
        "detalhes" => "$errstr em $errfile na linha $errline"
    ]);
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            "mensagem" => "Erro fatal no processamento.",
            "detalhes" => $error['message'] . " em " . $error['file'] . " na linha " . $error['line']
        ]);
    }
});

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["mensagem" => "Método não permitido."]);
    exit;
}

if (!isset($_FILES['arquivo'])) {
    http_response_code(400);
    echo json_encode(["mensagem" => "Nenhum arquivo enviado."]);
    exit;
}

$arquivoPath = $_FILES['arquivo']['tmp_name'];
$extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);

if (strtolower($extensao) !== 'csv') {
    http_response_code(400);
    echo json_encode(["mensagem" => "Formato inválido. Atualmente apenas .csv é suportado nativamente."]);
    exit;
}

try {
    $handle = fopen($arquivoPath, "r");
    if (!$handle) {
        throw new Exception("Não foi possível abrir o arquivo.");
    }

    $primeiraLinha = fgets($handle);
    $delimitador = (strpos($primeiraLinha, ';') !== false) ? ';' : ',';
    rewind($handle);

    fgetcsv($handle, 1000, $delimitador);

    $sucessoCount = 0;
    $erroCount = 0;
    $mensagensErro = [];
    $index = 0;

    while (($row = fgetcsv($handle, 1000, $delimitador)) !== false) {
        $index++;

        $row = array_map(function ($val) {
            return mb_check_encoding($val, 'UTF-8') ? $val : utf8_encode($val);
        }, $row);

        $setor_raw = trim($row[0] ?? '');
        $n_inventario = trim($row[1] ?? '');
        $dt_incorp = trim($row[2] ?? '');
        $denominacao_raw = trim($row[3] ?? '');
        $marca_raw = trim($row[4] ?? '');

        if (empty($n_inventario) || empty($denominacao_raw)) {
            if (!empty($n_inventario) || !empty($denominacao_raw)) {
                $erroCount++;
                $mensagensErro[] = "Linha " . ($index + 1) . ": Dados incompletos (Nº Identificação e Denominação são obrigatórios).";
            }
            continue;
        }

        $numero_identificacao = str_replace('.0', '', $n_inventario);

        $ano_fabricacao = null;
        if (preg_match('/(\d{4})/', $dt_incorp, $matches)) {
            $ano_fabricacao = intval($matches[1]);
        }

        $denominacao = mb_convert_case($denominacao_raw, MB_CASE_UPPER, "UTF-8");
        $marca = mb_convert_case($marca_raw, MB_CASE_UPPER, "UTF-8");
        $setor = mb_convert_case($setor_raw, MB_CASE_UPPER, "UTF-8");
        $modelo = "N/A";

        $stmt_check = $conn->prepare("SELECT id FROM maquinas WHERE numero_identificacao = ? LIMIT 1");
        $stmt_check->bind_param("s", $numero_identificacao);
        $stmt_check->execute();
        $exists = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        if ($exists) {
            $erroCount++;
            $mensagensErro[] = "Linha " . ($index + 1) . ": Máquina com Nº Identificação '$numero_identificacao' já cadastrada.";
            continue;
        }

        $sql = "INSERT INTO maquinas (denominacao, marca, modelo, numero_identificacao, ano_fabricacao, setor) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql);

        if ($stmt_insert) {
            $stmt_insert->bind_param("ssssis", $denominacao, $marca, $modelo, $numero_identificacao, $ano_fabricacao, $setor);
            if ($stmt_insert->execute()) {
                $sucessoCount++;
            } else {
                $erroCount++;
                $mensagensErro[] = "Linha " . ($index + 1) . ": Erro no banco - " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            $erroCount++;
            $mensagensErro[] = "Linha " . ($index + 1) . ": Erro ao preparar banco - " . $conn->error;
        }
    }

    fclose($handle);

    echo json_encode([
        "mensagem" => "Processamento concluído.",
        "sucesso" => $sucessoCount,
        "erros_count" => $erroCount,
        "detalhes_erro" => $mensagensErro
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro no processamento: " . $e->getMessage()]);
}
