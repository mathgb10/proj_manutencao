<?php require __DIR__ . '/../controllers/validar_acesso.php'; ?>
<?php require __DIR__ . '/../configs/conexao.php'; ?>
<?php require __DIR__ . '/../components/modals/all_modals.php'; ?>
<?php require __DIR__ . '/../components/modals/modal_os.php'; ?>
<?php
// Queries leves para filtros rápidos da O.S.
$tipos_os_lista = [];
$r = $conn->query("SELECT DISTINCT tipo FROM ordens_servico WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo ASC");
if ($r) { while ($row = $r->fetch_assoc()) $tipos_os_lista[] = $row['tipo']; }

$responsaveis_os_lista = [];
$r = $conn->query("SELECT DISTINCT u.nome FROM ordens_servico os INNER JOIN usuarios u ON os.responsavel_id = u.id ORDER BY u.nome ASC");
if ($r) { while ($row = $r->fetch_assoc()) $responsaveis_os_lista[] = $row['nome']; }
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar O.S - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../assets/icons/favicon.ico" type="image/x-icon">
    <style>
        .os-hist-observacao {
            border-left: 4px solid #607d8b !important;
        }

        .os-hist-observacao .os-timeline-status {
            background: #607d8b !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="page-actions-bar">
            <form action="" method="GET" class="page-search-form" onsubmit="event.preventDefault(); carregarOS();">
                <div class="page-search-box">
                    <input type="text" name="search" id="pesquisa-os" placeholder="Pesquisar O.S..."
                         oninput="carregarOS(1)">
                    <button type="button" class="page-clear-btn" id="btn-limpar-os" style="display: none;" onclick="limparBuscaOS()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
                
                <div class="page-filter-box">
                    <label for="select-filtro-os">Status:</label>
                    <select id="select-filtro-os" name="aba" onchange="filtrarOS()">
                        <option value="abertas">Abertas</option>
                        <option value="andamento">Andamento</option>
                        <option value="arquivadas">Arquivadas</option>
                        <option value="todas">Todas</option>
                    </select>
                </div>
                <?php if (!empty($tipos_os_lista)): ?>
                <div class="page-filter-box">
                    <label>Tipo:</label>
                    <select id="select-filtro-tipo-os" onchange="carregarOS(1)">
                        <option value="">Todos</option>
                        <?php foreach ($tipos_os_lista as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if (!empty($responsaveis_os_lista)): ?>
                <div class="page-filter-box">
                    <label>Responsável:</label>
                    <select id="select-filtro-responsavel-os" onchange="carregarOS(1)">
                        <option value="">Todos</option>
                        <?php foreach ($responsaveis_os_lista as $resp): ?>
                            <option value="<?= htmlspecialchars($resp) ?>"><?= htmlspecialchars($resp) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
            <button class="btn-page-action" onclick="showModal('novaOS')">
                <i class="bi bi-plus-lg"></i> Nova O.S.
            </button>
        </div>

        <!-- Tabela de O.S. -->
        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-file-earmark-text"></i>
                <h2 id="os-titulo-tabela">Ordens de Serviço - Abertas</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 7%;">Nº O.S.</th>
                            <th style="width: 28%;">DESCRIÇÃO</th>
                            <th style="width: 12%;">TIPO</th>
                            <th style="width: 14%;">SOLICITANTE</th>
                            <th style="width: 14%;">RESPONSÁVEL</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 15%; text-align: center;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-os-body">
                        <tr>
                            <td colspan="7"
                                style="text-align: center; padding: 40px; color: var(--corTxt3); opacity: 0.6;">
                                <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                                <p style="margin-top: 10px;">Carregando Ordens de Serviço...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginação AJAX Unificada -->
            <div class="div-btns-change" style="display: flex; justify-content: center; width: 100%; border-top: 1px solid var(--corBordas); padding: 10px 25px;">
                <div id="pagination-os-container" class="page-pagination" style="padding: 0;"></div>
            </div>
        </div>
    </section>

    <script src="../../js/scripts.js" defer></script>

    <script>
        // =============================================
        // SISTEMA DE ORDEM DE SERVIÇO - Frontend JS
        // =============================================

        // Dados do usuário logado (injetados pelo PHP para validação dos botões)
        const USUARIO_LOGADO_ID = <?= intval($_SESSION['user_id'] ?? 0) ?>;
        const USUARIO_PERMISSAO = "<?= htmlspecialchars($_SESSION['user_permissao'] ?? 'NORMAL') ?>";

        let abaAtual = 'abertas';
        let paginaAtualOS = 1;
        let osIdSelecionada = null;
        let osAtual = null; // dados completos da OS selecionada

        const TITULOS_ABA = {
            abertas: 'Ordens de Serviço - Abertas',
            andamento: 'Ordens de Serviço - Em Andamento',
            arquivadas: 'Ordens de Serviço - Arquivadas',
            todas: 'Ordens de Serviço - Todas'
        };

        document.addEventListener('DOMContentLoaded', () => {
            // Sincroniza a aba atual com o select (navegador pode manter o valor no F5)
            const selectAba = document.getElementById('select-filtro-os');
            if (selectAba) {
                abaAtual = selectAba.value;
                document.getElementById('os-titulo-tabela').textContent = TITULOS_ABA[abaAtual] || 'Ordens de Serviço';
            }
            carregarOS();

            // Verifica se há um ID de O.S. na URL para abrir automaticamente (vindo das notificações)
            const urlParams = new URLSearchParams(window.location.search);
            const osIdUrl = urlParams.get('os_id');
            if (osIdUrl) {
                verDetalheOS(osIdUrl);
            }
        });

        // ---------- FILTRO OS (PADRÃO NR12) ----------
        function filtrarOS() {
            const select = document.getElementById('select-filtro-os');
            if (select) {
                trocarAba(select.value);
            }
        }

        // ---------- TROCAR ABA ----------
        function trocarAba(aba) {
            abaAtual = aba;
            paginaAtualOS = 1; // Reseta página ao trocar aba

            // Sincroniza o select caso a troca venha de outro lugar
            const select = document.getElementById('select-filtro-os');
            if (select) select.value = aba;

            document.getElementById('os-titulo-tabela').textContent = TITULOS_ABA[aba] || 'Ordens de Serviço';
            carregarOS();
        }

        // ---------- CARREGAR / LISTAR ----------
        async function carregarOS(pagina = null) {
            if (pagina !== null) paginaAtualOS = pagina;

            const buscaInput = document.getElementById('pesquisa-os');
            const btnLimpar = document.getElementById('btn-limpar-os');
            const busca = buscaInput?.value || '';

            // Mostrar/Ocultar botão de limpar
            if (btnLimpar) {
                btnLimpar.style.display = busca ? 'block' : 'none';
            }

            const params = new URLSearchParams({
                aba: abaAtual,
                page: paginaAtualOS
            });
            if (busca) params.append('search', busca);

            // Filtros rápidos
            const filtroTipo = document.getElementById('select-filtro-tipo-os');
            const filtroResp = document.getElementById('select-filtro-responsavel-os');
            if (filtroTipo && filtroTipo.value) params.append('filtro_tipo', filtroTipo.value);
            if (filtroResp && filtroResp.value) params.append('filtro_responsavel', filtroResp.value);

            try {
                const res = await fetch(`../actions/os/listar_os.php?${params.toString()}`);
                const data = await res.json();

                if (data.success) {
                    renderizarTabela(data.dados, data.usuario_id, data.permissao);
                    renderizarPaginacaoOS(data.pagina_atual, data.total_paginas);
                    atualizarContadores(data.contadores);
                }
            } catch (err) {
                console.error('Erro ao carregar O.S.:', err);
            }
        }

        function renderizarPaginacaoOS(atual, total) {
            const container = document.getElementById('pagination-os-container');
            if (!container) return;

            let html = '';
            
            // Botão Anterior
            if (atual > 1) {
                html += `<button onclick="carregarOS(${atual - 1})" class="pag-btn"><i class="bi bi-chevron-left"></i> Anterior</button>`;
            } else {
                html += `<span class="pag-btn disabled"><i class="bi bi-chevron-left"></i> Anterior</span>`;
            }

            html += `<span class="pag-current">Página ${atual} de ${total}</span>`;

            // Botão Próximo
            if (atual < total) {
                html += `<button onclick="carregarOS(${atual + 1})" class="pag-btn">Próxima <i class="bi bi-chevron-right"></i></button>`;
            } else {
                html += `<span class="pag-btn disabled">Próxima <i class="bi bi-chevron-right"></i></span>`;
            }

            container.innerHTML = html;
        }

        function limparBuscaOS() {
            const buscaInput = document.getElementById('pesquisa-os');
            if (buscaInput) {
                buscaInput.value = '';
                paginaAtualOS = 1; // Reseta página ao limpar busca
                carregarOS();
            }
        }

        function renderizarTabela(ordens, usuarioId, permissao) {
            const tbody = document.getElementById('tabela-os-body');

            if (ordens.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--corTxt3); opacity: 0.6;">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p style="margin-top: 10px;">Nenhuma O.S. encontrada nesta aba</p>
                        </td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = ordens.map(os => {
                const statusClass = getStatusClass(os.status);
                const descResumida = os.descricao.length > 55 ? os.descricao.substring(0, 55) + '...' : os.descricao;
                const ehResponsavel = parseInt(os.responsavel_id) === parseInt(usuarioId);
                const ehAdmin = permissao === 'ADMIN';
                const ehGestor = permissao === 'GESTOR';
                const naoArquivada = os.status !== 'Arquivada';

                // Botão Ver Detalhes — sempre visível
                let btns = `
                    <button class="btnAcao editar" title="Ver Detalhes" onclick="verDetalheOS(${os.id})">
                        <i class="bi bi-eye-fill"></i>
                    </button>`;

                // Botão Aceitar — se for o responsável OU Admin/Gestor E status Em Aberto ou Aguardando
                if ((ehResponsavel || ehAdmin || ehGestor) && (os.status === 'Em Aberto' || os.status === 'Aguardando Aprovação')) {
                    btns += `
                    <button class="btnAcao confirmar" title="Aceitar" onclick="confirmarAceitarOS(${os.id})">
                        <i class="bi bi-check-lg"></i>
                    </button>`;
                }

                // Botão Recusar — se for o responsável OU Admin/Gestor E status Aguardando Aprovação
                if ((ehResponsavel || ehAdmin || ehGestor) && os.status === 'Aguardando Aprovação') {
                    btns += `
                    <button class="btnAcao deletar" title="Recusar" onclick="abrirRecusarOSTabela(${os.id})">
                        <i class="bi bi-x-lg"></i>
                    </button>`;
                }

                // Botão Arquivar — se for o responsável OU Admin/Gestor E não arquivada
                if ((ehAdmin || ehGestor || (ehResponsavel && os.status === 'Aceita')) && naoArquivada) {
                    btns += `
                    <button class="btnAcao deletar" title="Finalizar/Arquivar" onclick="confirmarArquivarOS(${os.id})" style="background:var(--corBase); color:white;">
                        <i class="bi bi-archive-fill"></i>
                    </button>`;
                }

                let maquinaInfo = '';
                if (os.maquina_nome) {
                    maquinaInfo = `<br><span style="font-size:0.75rem; color:var(--corTxt2); opacity:0.75; display:inline-flex; align-items:center; gap:4px; margin-top:2px;">
                        <i class="bi bi-gear" style="font-size:0.7rem;"></i> ${os.maquina_nome} ${os.patrimonio ? `(${os.patrimonio})` : ''}
                    </span>`;
                } else if (os.patrimonio) {
                    maquinaInfo = `<br><span style="font-size:0.75rem; color:var(--corTxt2); opacity:0.75; display:inline-flex; align-items:center; gap:4px; margin-top:2px;">
                        <i class="bi bi-tag" style="font-size:0.7rem;"></i> NI/Patrimônio: ${os.patrimonio}
                    </span>`;
                }

                return `
                    <tr>
                        <td style="font-weight: bold; color: var(--corBase); white-space: nowrap;">
                            #${os.id}
                            ${parseInt(os.total_anexos || 0) > 0 ? '<i class="bi bi-paperclip" title="Possui anexo" style="color:var(--corBase); font-size:1.15rem; margin-left:4px; vertical-align: middle;"></i>' : ''}
                        </td>
                        <td style="text-align: left; padding-left: 10px;" title="${os.descricao}">
                            ${descResumida}
                            ${maquinaInfo}
                        </td>
                        <td><span class="os-tipo-badge os-tipo-${os.tipo.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')}">${os.tipo}</span></td>
                        <td>${os.solicitante_nome}</td>
                        <td>${os.responsavel_nome}</td>
                        <td><span class="os-status-badge ${statusClass}">${os.status}</span></td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 5px; flex-wrap: wrap;">
                                ${btns}
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        function atualizarContadores(contadores) {
            // Os contadores agora são omitidos para manter a interface limpa (Design Premium)
        }

        function getStatusClass(status) {
            const map = {
                'Em Aberto': 'os-status-aberto',
                'Aguardando Aprovação': 'os-status-aguardando',
                'Aceita': 'os-status-aceita',
                'Arquivada': 'os-status-arquivada',
                'Recusada': 'os-status-recusada'
            };
            return map[status] || 'os-status-aberto';
        }

        function formatarData(dataStr) {
            if (!dataStr) return '';
            const d = new Date(dataStr);
            return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // ---------- BUSCA DE PATRIMÔNIO ----------
        let patrimonioTimeout = null;

        async function buscarPatrimonio(termo) {
            clearTimeout(patrimonioTimeout);
            const container = document.getElementById('patrimonio-resultados');

            if (!termo || termo.length < 2) {
                if (container) container.style.display = 'none';
                return;
            }

            patrimonioTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(`../actions/machines/listar_maquinas.php?search=${encodeURIComponent(termo)}`);
                    const data = await res.json();

                    if (!container) return;

                    if (data.success && data.dados && data.dados.length > 0) {
                        container.innerHTML = data.dados.slice(0, 8).map(m => {
                            const mNome = m.nome || m.descricao || 'Equipamento';
                            const mPatrimonio = m.patrimonio !== null && m.patrimonio !== undefined ? m.patrimonio.toString() : m.id.toString();
                            const safeNome = mNome.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                            const safePatrimonio = mPatrimonio.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                            return `
                            <div onclick="selecionarPatrimonioDesteElemento(this)"
                                data-valor="${safePatrimonio}"
                                data-label="${safeNome}"
                                style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--corBordas);transition:.2s;"
                                onmouseenter="this.style.background='var(--corFundo2)'"
                                onmouseleave="this.style.background=''"
                            >
                                <strong>${mNome}</strong>
                                <span style="font-size:.8rem;opacity:.7;margin-left:8px;">${mPatrimonio}</span>
                            </div>
                            `;
                        }).join('');
                        container.style.display = 'block';
                    } else {
                        const safeTermo = termo.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        container.innerHTML = `<div style="padding:10px 14px;opacity:.6;">
                            <i class="bi bi-info-circle"></i> Nenhum equipamento encontrado. Clique abaixo para usar o texto digitado.
                            <div onclick="selecionarPatrimonio('${safeTermo}','${safeTermo}')"
                                style="margin-top:6px;padding:6px 10px;background:var(--corBase);color:#fff;border-radius:6px;cursor:pointer;text-align:center;font-size:.85rem;">
                                Usar: "${termo}"
                            </div>
                        </div>`;
                        container.style.display = 'block';
                    }
                } catch (e) {
                    if (container) container.style.display = 'none';
                }
            }, 350);
        }

        function selecionarPatrimonioDesteElemento(el) {
            const valor = el.getAttribute('data-valor');
            const label = el.getAttribute('data-label');
            selecionarPatrimonio(valor, label);
        }

        function selecionarPatrimonio(valor, label) {
            const inputPat = document.getElementById('os_patrimonio');
            if (inputPat) inputPat.value = valor;

            const inputBusca = document.getElementById('os_patrimonio_busca');
            if (inputBusca) inputBusca.value = label;

            const container = document.getElementById('patrimonio-resultados');
            if (container) container.style.display = 'none';

            const selecionado = document.getElementById('patrimonio-selecionado');
            if (selecionado) selecionado.style.display = 'block';

            const texto = document.getElementById('patrimonio-selecionado-texto');
            if (texto) texto.textContent = label + ' (Nº: ' + valor + ')';
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#novaOS')) {
                const r = document.getElementById('patrimonio-resultados');
                if (r) r.style.display = 'none';
            }
        });

        // ---------- CRIAR O.S. ----------
        async function criarOS() {
            const descricao = document.getElementById('os_descricao').value.trim();
            const tipo = document.getElementById('os_tipo').value;
            const patrimonio = document.getElementById('os_patrimonio').value.trim();
            const anexoInput = document.getElementById('os_anexo');
            const anexo = anexoInput ? anexoInput.files[0] : null;

            if (!descricao || !tipo) {
                alert('Preencha Descrição e Tipo!');
                return;
            }

            const formData = new FormData();
            formData.append('descricao', descricao);
            formData.append('tipo', tipo);
            formData.append('patrimonio', patrimonio);
            if (anexo) {
                formData.append('anexo', anexo);
            }

            try {
                const res = await fetch('../actions/os/criar_os.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    closeModal('novaOS');
                    document.getElementById('os_descricao').value = '';
                    document.getElementById('os_tipo').value = '';
                    document.getElementById('os_patrimonio').value = '';
                    document.getElementById('os_maquina_id').value = '';
                    removerAnexoSelecionado();
                    // Reset display da máquina selecionada
                    const txt = document.getElementById('maquina-selecionada-texto');
                    if (txt) {
                        txt.textContent = 'Clique para selecionar uma máquina...';
                        txt.style.opacity = '.55';
                    }
                    trocarAba('abertas');
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao criar O.S.:', err);
                alert('Erro ao criar Ordem de Serviço');
            }
        }

        // ---------- VER DETALHE ----------
        async function verDetalheOS(id) {
            osIdSelecionada = id;
            try {
                const res = await fetch(`../actions/os/detalhe_os.php?id=${id}`);
                const data = await res.json();

                if (data.success) {
                    osAtual = data.dados;
                    const os = data.dados;

                    document.getElementById('detalhe-os-titulo').textContent = `Ordem de Serviço: #${os.id}`;
                    document.getElementById('detalhe-os-data').textContent = formatarData(os.criado_em);
                    document.getElementById('detalhe-os-solicitante').textContent = os.solicitante_nome;
                    document.getElementById('detalhe-os-tipo').textContent = os.tipo;
                    document.getElementById('detalhe-os-status').innerHTML = `<span class="os-status-badge ${getStatusClass(os.status)}">${os.status}</span>`;
                    const maquinaInfoStr = os.maquina_nome ? `${os.maquina_nome} (${os.patrimonio || 'Sem NI'})` : (os.patrimonio || 'Não informado');
                    document.getElementById('detalhe-os-patrimonio').textContent = maquinaInfoStr;
                    document.getElementById('detalhe-os-descricao').textContent = os.descricao;
                    document.getElementById('detalhe-os-origem').textContent = os.solicitante_nome;
                    document.getElementById('detalhe-os-destino').textContent = os.responsavel_nome;
                    document.getElementById('detalhe-os-destino-fluxo').textContent = os.responsavel_nome;
                    document.getElementById('encaminhar_os_id').value = os.id;

                    // Campos gasto + obs (só visíveis se Arquivada)
                    const isArquivada = os.status === 'Arquivada';
                    document.getElementById('detalhe-os-bloco-gasto').style.display = isArquivada ? '' : 'none';
                    document.getElementById('detalhe-os-bloco-obs').style.display = isArquivada ? '' : 'none';
                    if (isArquivada) {
                        document.getElementById('detalhe-os-gasto').textContent = os.gasto ? 'R$ ' + parseFloat(os.gasto).toFixed(2).replace('.', ',') : 'Não informado';
                        document.getElementById('detalhe-os-obs').textContent = os.obs_finalizacao || 'Não informado';
                    }

                    // Anexos
                    const anexosDiv = document.getElementById('detalhe-os-anexos-lista');
                    if (os.anexos && os.anexos.length > 0) {
                        anexosDiv.innerHTML = os.anexos.map(a => `
                            <a href="${a.caminho}" target="_blank" class="os-anexo-link">
                                <i class="bi bi-file-earmark"></i> ${a.nome_arquivo}
                            </a>
                        `).join('');
                    } else {
                        anexosDiv.innerHTML = '<span style="color: var(--corTxt3); opacity: 0.6;">Nenhum anexo encontrado</span>';
                    }

                    // Mostrar botões condicionais
                    const ehResponsavel = parseInt(os.responsavel_id) === USUARIO_LOGADO_ID;
                    const ehAdmin = USUARIO_PERMISSAO === 'ADMIN';
                    const ehGestor = USUARIO_PERMISSAO === 'GESTOR';
                    const aceita = os.status === 'Aceita';
                    const aguardando = os.status === 'Aguardando Aprovação';
                    const naoArquivada = os.status !== 'Arquivada';

                    document.getElementById('btn-observacao-os').style.display = ((ehResponsavel || ehAdmin) && naoArquivada) ? '' : 'none';
                    document.getElementById('btn-encaminhar-os').style.display = ((ehAdmin || ehGestor || (ehResponsavel && (aceita || aguardando))) && naoArquivada) ? '' : 'none';

                    showModal('detalheOS');
                }
            } catch (err) {
                console.error('Erro ao carregar detalhe:', err);
            }
        }

        // ---------- ACEITAR ----------
        async function aceitarOS() {
            const id = document.getElementById('aceitar_os_id').value || osIdSelecionada;
            if (!id) return;

            try {
                const res = await fetch('../actions/os/aceitar_os.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        os_id: parseInt(id)
                    })
                });
                const data = await res.json();

                closeModal('aceitarOSModal');
                closeModal('detalheOS');

                if (data.success) {
                    if (abaAtual !== 'todas') trocarAba('andamento');
                    else carregarOS();
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao aceitar O.S.:', err);
            }
        }

        function confirmarAceitarOS(id) {
            document.getElementById('aceitar_os_id').value = id;
            showModal('aceitarOSModal');
        }

        // ---------- ENCAMINHAR ----------
        async function encaminharOS() {
            const os_id = document.getElementById('encaminhar_os_id').value || osIdSelecionada;
            const responsavel_id = document.getElementById('encaminhar_responsavel').value;
            const motivo = document.getElementById('encaminhar_motivo').value.trim();
            const anexoInput = document.getElementById('enc_anexo');
            const anexo = anexoInput ? anexoInput.files[0] : null;

            if (!responsavel_id || !motivo) {
                alert('Preencha todos os campos!');
                return;
            }

            const formData = new FormData();
            formData.append('os_id', os_id);
            formData.append('responsavel_id', responsavel_id);
            formData.append('motivo', motivo);
            if (anexo) {
                formData.append('anexo', anexo);
            }

            try {
                const res = await fetch('../actions/os/encaminhar_os.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                closeModal('encaminharOS');
                closeModal('detalheOS');
                document.getElementById('encaminhar_motivo').value = '';
                document.getElementById('encaminhar_responsavel').value = '';
                removerEncAnexoSelecionado();

                if (data.success) {
                    trocarAba('andamento');
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao encaminhar O.S.:', err);
            }
        }

        // ---------- ARQUIVAR ----------
        function abrirArquivarOSNoDetalhe() {
            confirmarArquivarOS(osIdSelecionada);
        }

        function confirmarArquivarOS(id) {
            document.getElementById('arquivar_os_id').value = id;
            document.getElementById('arquivar_gasto').value = '';
            document.getElementById('arquivar_obs').value = '';
            showModal('arquivarOSModal');
        }

        async function arquivarOS() {
            const id = document.getElementById('arquivar_os_id').value;
            const gasto = document.getElementById('arquivar_gasto').value;
            const obs = document.getElementById('arquivar_obs').value.trim();

            try {
                const res = await fetch('../actions/os/arquivar_os.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        os_id: parseInt(id),
                        gasto: gasto !== '' ? parseFloat(gasto) : null,
                        obs_finalizacao: obs
                    })
                });
                const data = await res.json();

                closeModal('arquivarOSModal');
                closeModal('detalheOS');
                if (data.success) {
                    trocarAba('arquivadas');
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao arquivar O.S.:', err);
            }
        }

        // ---------- RECUSAR ----------
        function abrirRecusarOS() {
            // Chamado pelo botão dentro do detalhe
            document.getElementById('recusar_os_id').value = osIdSelecionada;
            document.getElementById('recusar_motivo').value = '';
            showModal('recusarOSModal');
        }

        function abrirRecusarOSTabela(id) {
            // Chamado diretamente da tabela
            osIdSelecionada = id;
            document.getElementById('recusar_os_id').value = id;
            document.getElementById('recusar_motivo').value = '';
            showModal('recusarOSModal');
        }

        async function recusarOS() {
            const id = document.getElementById('recusar_os_id').value;
            const motivo = document.getElementById('recusar_motivo').value.trim();

            if (!motivo) {
                alert('Informe o motivo da recusa!');
                return;
            }

            try {
                const res = await fetch('../actions/os/recusar_os.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        os_id: parseInt(id),
                        motivo_recusa: motivo
                    })
                });
                const data = await res.json();

                closeModal('recusarOSModal');
                closeModal('detalheOS');

                if (data.success) {
                    trocarAba('abertas');
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao recusar O.S.:', err);
            }
        }

        // ---------- OBSERVAÇÃO ----------
        function abrirModalObservacao() {
            document.getElementById('obs_os_id').value = osIdSelecionada;
            document.getElementById('campo_observacao').value = '';
            removerObsAnexoSelecionado();
            showModal('observacaoOSModal');
        }

        async function salvarObservacao() {
            const id = document.getElementById('obs_os_id').value;
            const obs = document.getElementById('campo_observacao').value.trim();
            const anexoInput = document.getElementById('obs_anexo');
            const anexo = anexoInput ? anexoInput.files[0] : null;

            if (!obs) {
                alert('Digite uma observação para salvar!');
                return;
            }

            const formData = new FormData();
            formData.append('os_id', id);
            formData.append('observacao', obs);
            if (anexo) {
                formData.append('anexo', anexo);
            }

            try {
                const res = await fetch('../actions/os/adicionar_observacao.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                closeModal('observacaoOSModal');
                removerObsAnexoSelecionado();
                
                if (data.success) {
                    exibirSucesso(data.message);
                    if (typeof verDetalheOS === 'function' && typeof osIdSelecionada !== 'undefined' && osIdSelecionada) {
                        verDetalheOS(osIdSelecionada);
                    }
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao salvar observação:', err);
                alert('Erro ao salvar observação');
            }
        }

        // ---------- HISTÓRICO ----------
        async function verHistoricoOS() {
            const id = osIdSelecionada;
            if (!id) return;

            try {
                const res = await fetch(`../actions/os/get_historico.php?os_id=${id}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('historico-os-titulo').textContent = `Histórico da O.S.: #${id}`;
                    const timeline = document.getElementById('historico-os-timeline');

                    if (data.dados.length === 0) {
                        timeline.innerHTML = '<p style="text-align: center; color: var(--corTxt3); opacity: 0.6;">Nenhum registro no histórico</p>';
                    } else {
                        timeline.innerHTML = data.dados.map(h => {
                            const statusClass = h.status.includes('Criada') ? 'os-hist-criada' :
                                h.status.includes('Aceita') ? 'os-hist-aceita' :
                                h.status.includes('Encaminhada') ? 'os-hist-encaminhada' :
                                h.status.includes('Arquivada') ? 'os-hist-arquivada' :
                                h.status.includes('Recusada') ? 'os-hist-recusada' :
                                h.status.includes('Observação') ? 'os-hist-observacao' : '';

                            let anexosHtml = '';
                            if (h.anexos) {
                                const listaAnexos = h.anexos.split(';;');
                                anexosHtml = `<div class="os-hist-anexos" style="margin-top:10px; display:flex; flex-direction:column; gap:6px; border-top:1px dashed var(--corBordas); padding-top:8px;">
                                    <span style="font-size:0.75rem; font-weight:bold; color:var(--corTxt3); opacity:0.7; display:flex; align-items:center; gap:4px;">
                                        <i class="bi bi-paperclip"></i> Anexos vinculados:
                                    </span>
                                    ${listaAnexos.map(anexoStr => {
                                        const partes = anexoStr.split('||');
                                        const nome = partes[0] || 'Arquivo';
                                        const caminho = partes[1] || '#';
                                        return `<a href="${caminho}" target="_blank" class="os-anexo-link" style="font-size:0.8rem; color:var(--corBase); text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:0.2s;" onmouseenter="this.style.opacity=0.8" onmouseleave="this.style.opacity=1">
                                            <i class="bi bi-file-earmark-arrow-down"></i> ${nome}
                                        </a>`;
                                    }).join('')}
                                </div>`;
                            }

                            return `
                                <div class="os-timeline-item ${statusClass}">
                                    <div class="os-timeline-header">
                                        <span class="os-timeline-data">${formatarData(h.criado_em)}</span>
                                        <span class="os-timeline-status">${h.status}</span>
                                    </div>
                                    <div class="os-timeline-body">
                                        <p><strong>De:</strong> ${h.origem_nome} &nbsp;|&nbsp; <strong>Para:</strong> ${h.destino_nome}</p>
                                        <p class="os-timeline-desc">${h.descricao}</p>
                                        ${anexosHtml}
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }

                    showModal('historicoOS');
                }
            } catch (err) {
                console.error('Erro ao carregar histórico:', err);
            }
        }
        // =============================================
        // MODAL PESQUISA DE MÃQUINA
        // =============================================
        let _maquinasTodas = []; // cache da lista inicial
        let _maquinasBuscaTimeout = null;
        let _maquinasBuscaSeq = 0;

        async function abrirModalMaquinas() {
            clearTimeout(_maquinasBuscaTimeout);
            _maquinasBuscaSeq++;
            document.getElementById('maquina-modal-busca').value = '';
            showModal('modalPesquisaMaquina');

            // Carrega/usa cache
            if (_maquinasTodas.length === 0) {
                await _carregarTodasMaquinas();
            } else {
                _renderMaquinas(_maquinasTodas);
            }
        }

        async function _carregarTodasMaquinas() {
            const lista = document.getElementById('maquina-modal-lista');
            lista.innerHTML = `<div style="text-align:center;padding:30px;opacity:.5;">
                <i class="bi bi-hourglass-split" style="font-size:2rem;"></i>
                <p style="margin-top:8px;">Carregando máquinas...</p></div>`;

            try {
                const res = await fetch('../actions/machines/listar_maquinas.php?search=');
                const data = await res.json();
                _maquinasTodas = data.dados || [];
                _renderMaquinas(_maquinasTodas);
            } catch (e) {
                lista.innerHTML = `<div style="text-align:center;padding:20px;color:var(--corBase);">
                    <i class="bi bi-exclamation-triangle" style="font-size:2rem;"></i>
                    <p>Erro ao carregar máquinas.</p></div>`;
            }
        }

        async function filtrarMaquinasModal(termo) {
            clearTimeout(_maquinasBuscaTimeout);
            const busca = termo.trim();

            if (busca === '') {
                _maquinasBuscaSeq++;
                _renderMaquinas(_maquinasTodas);
                return;
            }

            const buscaSeq = ++_maquinasBuscaSeq;
            _maquinasBuscaTimeout = setTimeout(async () => {
                const lista = document.getElementById('maquina-modal-lista');
                lista.innerHTML = `<div style="text-align:center;padding:30px;opacity:.5;">
                    <i class="bi bi-hourglass-split" style="font-size:2rem;"></i>
                    <p style="margin-top:8px;">Buscando mÃ¡quinas...</p></div>`;

                try {
                    const res = await fetch(`../actions/machines/listar_maquinas.php?search=${encodeURIComponent(busca)}`);
                    const data = await res.json();
                    if (buscaSeq !== _maquinasBuscaSeq) return;
                    _renderMaquinas(data.dados || []);
                } catch (e) {
                    if (buscaSeq !== _maquinasBuscaSeq) return;
                    lista.innerHTML = `<div style="text-align:center;padding:20px;color:var(--corBase);">
                        <i class="bi bi-exclamation-triangle" style="font-size:2rem;"></i>
                        <p>Erro ao buscar mÃ¡quinas.</p></div>`;
                }
            }, 250);
        }

        function limparBuscaMaquinasModal() {
            const input = document.getElementById('maquina-modal-busca');
            if (input) {
                input.value = '';
                input.focus();
            }
            clearTimeout(_maquinasBuscaTimeout);
            _maquinasBuscaSeq++;
            _renderMaquinas(_maquinasTodas);
        }

        function _renderMaquinas(lista) {
            const el = document.getElementById('maquina-modal-lista');

            if (!lista.length) {
                el.innerHTML = `<div style="text-align:center;padding:30px;opacity:.5;">
                    <i class="bi bi-search" style="font-size:2rem;"></i>
                    <p style="margin-top:8px;">Nenhuma máquina encontrada.</p></div>`;
                return;
            }

            el.innerHTML = lista.map(m => {
                const mNome = m.nome || '';
                const mPatrimonio = m.patrimonio !== null && m.patrimonio !== undefined ? m.patrimonio.toString() : '';
                const safeNome = mNome.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                const safePatrimonio = mPatrimonio.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

                return `
                <div onclick="selecionarMaquinaDesteElemento(this)"
                    data-id="${m.id}"
                    data-nome="${safeNome}"
                    data-patrimonio="${safePatrimonio}"
                    style="
                        display:flex;align-items:center;gap:14px;
                        padding:12px 16px;border-radius:10px;
                        background:var(--corFundo);border:1.5px solid var(--corBordas);
                        cursor:pointer;transition:.2s;"
                    onmouseenter="this.style.borderColor='var(--corBase)';this.style.background='rgba(252,35,35,.05)'"
                    onmouseleave="this.style.borderColor='var(--corBordas)';this.style.background='var(--corFundo)'">
                    <div style="width:42px;height:42px;border-radius:10px;background:rgba(252,35,35,.1);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-cpu" style="font-size:1.3rem;color:var(--corBase);"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;color:var(--corTxt3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${mNome || 'Sem Nome'}</div>
                        <div style="font-size:.8rem;color:var(--corTxt3);opacity:.6;">
                            NI: ${mPatrimonio || 'Não informado'}
                            ${m.setor ? ' &nbsp;|&nbsp; Setor: ' + m.setor : ''}
                        </div>
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--corBase);opacity:.5;"></i>
                </div>
                `;
            }).join('');
        }

        function selecionarMaquinaDesteElemento(el) {
            const id = el.getAttribute('data-id');
            const nome = el.getAttribute('data-nome');
            const patrimonio = el.getAttribute('data-patrimonio');
            selecionarMaquina(id, nome, patrimonio);
        }

        function selecionarMaquina(id, nome, patrimonio) {
            // Preenche os campos hidden
            const inputId = document.getElementById('os_maquina_id');
            if (inputId) inputId.value = id;

            const inputPat = document.getElementById('os_patrimonio');
            if (inputPat) inputPat.value = patrimonio;

            // Atualiza o display
            const box = document.getElementById('maquina-selecionada-texto');
            if (box) {
                box.textContent = '';
                box.style.opacity = '1';

                // Ícone check + nome + patrimônio
                const icon = document.createElement('i');
                icon.className = 'bi bi-check-circle-fill';
                icon.style.color = 'var(--confirmar)';
                icon.style.marginRight = '6px';
                box.appendChild(icon);
                box.appendChild(document.createTextNode(`${nome}  —  NI: ${patrimonio}`));
            }

            // Fecha modal de pesquisa
            closeModal('modalPesquisaMaquina');
        }

        function atualizarFeedbackAnexo(input) {
            const container = document.getElementById('anexo-upload-container');
            const box = document.getElementById('anexo-selecionado-box');
            const nomeSpan = document.getElementById('anexo-nome-arquivo');
            const icon = document.getElementById('anexo-icon');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                nomeSpan.textContent = file.name;
                
                const ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    icon.className = 'bi bi-file-earmark-pdf-fill';
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    icon.className = 'bi bi-file-earmark-image-fill';
                } else {
                    icon.className = 'bi bi-file-earmark-fill';
                }

                if (container) container.style.display = 'none';
                if (box) box.style.display = 'flex';
            } else {
                removerAnexoSelecionado();
            }
        }

        function removerAnexoSelecionado() {
            const input = document.getElementById('os_anexo');
            if (input) input.value = '';

            const container = document.getElementById('anexo-upload-container');
            const box = document.getElementById('anexo-selecionado-box');

            if (container) container.style.display = 'flex';
            if (box) box.style.display = 'none';
        }

        function atualizarFeedbackObsAnexo(input) {
            const container = document.getElementById('obs-anexo-upload-container');
            const box = document.getElementById('obs-anexo-selecionado-box');
            const nomeSpan = document.getElementById('obs-anexo-nome-arquivo');
            const icon = document.getElementById('obs-anexo-icon');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                nomeSpan.textContent = file.name;
                
                const ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    icon.className = 'bi bi-file-earmark-pdf-fill';
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    icon.className = 'bi bi-file-earmark-image-fill';
                } else {
                    icon.className = 'bi bi-file-earmark-fill';
                }

                if (container) container.style.display = 'none';
                if (box) box.style.display = 'flex';
            } else {
                removerObsAnexoSelecionado();
            }
        }

        function removerObsAnexoSelecionado() {
            const input = document.getElementById('obs_anexo');
            if (input) input.value = '';

            const container = document.getElementById('obs-anexo-upload-container');
            const box = document.getElementById('obs-anexo-selecionado-box');

            if (container) container.style.display = 'flex';
            if (box) box.style.display = 'none';
        }

        function atualizarFeedbackEncAnexo(input) {
            const container = document.getElementById('enc-anexo-upload-container');
            const box = document.getElementById('enc-anexo-selecionado-box');
            const nomeSpan = document.getElementById('enc-anexo-nome-arquivo');
            const icon = document.getElementById('enc-anexo-icon');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                nomeSpan.textContent = file.name;
                
                const ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    icon.className = 'bi bi-file-earmark-pdf-fill';
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    icon.className = 'bi bi-file-earmark-image-fill';
                } else {
                    icon.className = 'bi bi-file-earmark-fill';
                }

                if (container) container.style.display = 'none';
                if (box) box.style.display = 'flex';
            } else {
                removerEncAnexoSelecionado();
            }
        }

        function removerEncAnexoSelecionado() {
            const input = document.getElementById('enc_anexo');
            if (input) input.value = '';

            const container = document.getElementById('enc-anexo-upload-container');
            const box = document.getElementById('enc-anexo-selecionado-box');

            if (container) container.style.display = 'flex';
            if (box) box.style.display = 'none';
        }
    </script>

</body>

</html>
