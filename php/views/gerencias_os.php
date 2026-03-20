<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../configs/conexao.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>
<?php require '../components/modals/modal_os.php'; ?>

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
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require '../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require '../components/header.php'; ?>

        <!-- Barra de Ações -->
        <div class="div-btns-pages">
            <form action="" method="GET" class="form-pesquisa" onsubmit="event.preventDefault(); carregarOS();">
                <div class="search-container">
                    <div class="box-pesquisa">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa-os" placeholder="Pesquisar O.S..."
                            class="input-pesquisa" oninput="carregarOS()">
                    </div>
                </div>
            </form>
            <button class="btn" onclick="showModal('novaOS')">
                <i class="bi bi-plus-lg"></i> Nova O.S.
            </button>
        </div>

        <!-- Cards de Resumo — 3 Abas -->
        <div class="os-resumo-container">
            <div class="os-resumo-card os-resumo-aberto os-tab-ativo" onclick="trocarAba('abertas')">
                <div class="os-resumo-label"><i class="bi bi-inbox"></i> Abertas</div>
                <div class="os-resumo-numero" id="contador-abertas">0</div>
            </div>
            <div class="os-resumo-card os-resumo-aguardando" onclick="trocarAba('andamento')">
                <div class="os-resumo-label"><i class="bi bi-hourglass-split"></i> Andamento</div>
                <div class="os-resumo-numero" id="contador-andamento">0</div>
            </div>
            <div class="os-resumo-card os-resumo-arquivada" onclick="trocarAba('arquivadas')">
                <div class="os-resumo-label"><i class="bi bi-archive"></i> Arquivadas</div>
                <div class="os-resumo-numero" id="contador-arquivadas">0</div>
            </div>
        </div>

        <!-- Tabela de O.S. -->
        <div class="tabela-bg2" style="height: 55vh;">
            <div class="tabela-titulo">
                <i class="bi bi-file-earmark-text"></i>
                <h2 id="os-titulo-tabela">Ordens de Serviço — Abertas</h2>
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
        let osIdSelecionada = null;
        let osAtual = null; // dados completos da OS selecionada

        const TITULOS_ABA = {
            abertas:    'Ordens de Serviço — Abertas',
            andamento:  'Ordens de Serviço — Em Andamento',
            arquivadas: 'Ordens de Serviço — Arquivadas'
        };

        document.addEventListener('DOMContentLoaded', () => {
            carregarOS();
        });

        // ---------- TROCAR ABA ----------
        function trocarAba(aba) {
            abaAtual = aba;

            document.querySelectorAll('.os-resumo-card').forEach(c => c.classList.remove('os-tab-ativo'));
            const mapCard = { abertas: '.os-resumo-aberto', andamento: '.os-resumo-aguardando', arquivadas: '.os-resumo-arquivada' };
            document.querySelector(mapCard[aba])?.classList.add('os-tab-ativo');

            document.getElementById('os-titulo-tabela').textContent = TITULOS_ABA[aba] || 'Ordens de Serviço';
            carregarOS();
        }

        // ---------- CARREGAR / LISTAR ----------
        async function carregarOS() {
            const busca  = document.getElementById('pesquisa-os')?.value || '';
            const params = new URLSearchParams({ aba: abaAtual });
            if (busca) params.append('search', busca);

            try {
                const res  = await fetch(`../actions/os/listar_os.php?${params.toString()}`);
                const data = await res.json();

                if (data.success) {
                    renderizarTabela(data.dados, data.usuario_id, data.permissao);
                    atualizarContadores(data.contadores);
                }
            } catch (err) {
                console.error('Erro ao carregar O.S.:', err);
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
                const statusClass   = getStatusClass(os.status);
                const descResumida  = os.descricao.length > 55 ? os.descricao.substring(0, 55) + '...' : os.descricao;
                const ehResponsavel = parseInt(os.responsavel_id) === parseInt(usuarioId);
                const ehAdmin       = permissao === 'ADMIN';

                // Botão Ver Detalhes — sempre visível
                let btns = `
                    <button class="btnAcao editar" title="Ver Detalhes" onclick="verDetalheOS(${os.id})">
                        <i class="bi bi-eye-fill"></i>
                    </button>`;

                // Botão Aceitar — se for o responsável E status for Em Aberto ou Aguardando
                if ((ehResponsavel || ehAdmin) && (os.status === 'Em Aberto' || os.status === 'Aguardando Aprovação')) {
                    btns += `
                    <button class="btnAcao confirmar" title="Aceitar" onclick="confirmarAceitarOS(${os.id})">
                        <i class="bi bi-check-lg"></i>
                    </button>`;
                }

                // Botão Recusar — se for o responsável E status = Aguardando Aprovação
                if ((ehResponsavel || ehAdmin) && os.status === 'Aguardando Aprovação') {
                    btns += `
                    <button class="btnAcao deletar" title="Recusar" onclick="abrirRecusarOSTabela(${os.id})">
                        <i class="bi bi-x-lg"></i>
                    </button>`;
                }

                // Botão Arquivar — se for o responsável E status = Aceita
                if ((ehResponsavel || ehAdmin) && os.status === 'Aceita') {
                    btns += `
                    <button class="btnAcao deletar" title="Finalizar/Arquivar" onclick="confirmarArquivarOS(${os.id})">
                        <i class="bi bi-archive-fill"></i>
                    </button>`;
                }

                return `
                    <tr>
                        <td style="font-weight: bold; color: var(--corBase);">#${os.id}</td>
                        <td style="text-align: left; padding-left: 10px;" title="${os.descricao}">${descResumida}</td>
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
            document.getElementById('contador-abertas').textContent    = contadores.abertas    ?? 0;
            document.getElementById('contador-andamento').textContent  = contadores.andamento  ?? 0;
            document.getElementById('contador-arquivadas').textContent = contadores.arquivadas ?? 0;
        }

        function getStatusClass(status) {
            const map = {
                'Em Aberto':            'os-status-aberto',
                'Aguardando Aprovação': 'os-status-aguardando',
                'Aceita':               'os-status-aceita',
                'Arquivada':            'os-status-arquivada',
                'Recusada':             'os-status-recusada'
            };
            return map[status] || 'os-status-aberto';
        }

        function formatarData(dataStr) {
            if (!dataStr) return '';
            const d = new Date(dataStr);
            return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }

        // ---------- BUSCA DE PATRIMÔNIO NA MODAL ----------
        let patrimonioTimeout = null;

        async function buscarPatrimonio(termo) {
            clearTimeout(patrimonioTimeout);
            const container = document.getElementById('patrimonio-resultados');

            if (!termo || termo.length < 2) {
                container.style.display = 'none';
                return;
            }

            patrimonioTimeout = setTimeout(async () => {
                try {
                    const res  = await fetch(`../actions/machines/listar_maquinas.php?search=${encodeURIComponent(termo)}`);
                    const data = await res.json();

                    if (data.success && data.dados && data.dados.length > 0) {
                        container.innerHTML = data.dados.slice(0, 8).map(m =>
                            `<div onclick="selecionarPatrimonio('${m.patrimonio || m.id}', '${(m.nome || m.descricao || 'Equipamento').replace(/'/g,"\\'")}')
                                style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--corBordas);transition:.2s;"
                                onmouseenter="this.style.background='var(--corFundo2)'"
                                onmouseleave="this.style.background=''"
                            >
                                <strong>${m.nome || m.descricao || 'Equipamento'}</strong>
                                <span style="font-size:.8rem;opacity:.7;margin-left:8px;">${m.patrimonio || '#' + m.id}</span>
                            </div>`
                        ).join('');
                        container.style.display = 'block';
                    } else {
                        // Permite digitar livremente mesmo sem resultado
                        container.innerHTML = `<div style="padding:10px 14px;opacity:.6;">
                            <i class="bi bi-info-circle"></i> Nenhum equipamento encontrado. Clique abaixo para usar o texto digitado.
                            <div onclick="selecionarPatrimonio('${termo}','${termo}')"
                                style="margin-top:6px;padding:6px 10px;background:var(--corBase);color:#fff;border-radius:6px;cursor:pointer;text-align:center;font-size:.85rem;">
                                Usar: "${termo}"
                            </div>
                        </div>`;
                        container.style.display = 'block';
                    }
                } catch(e) {
                    // Falha silenciosa na busca
                    container.style.display = 'none';
                }
            }, 350);
        }

        function selecionarPatrimonio(valor, label) {
            document.getElementById('os_patrimonio').value        = valor;
            document.getElementById('os_patrimonio_busca').value  = label;
            document.getElementById('patrimonio-resultados').style.display = 'none';
            document.getElementById('patrimonio-selecionado').style.display = 'block';
            document.getElementById('patrimonio-selecionado-texto').textContent = label + ' (Nº: ' + valor + ')';
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
            const descricao     = document.getElementById('os_descricao').value.trim();
            const tipo          = document.getElementById('os_tipo').value;
            const patrimonio    = document.getElementById('os_patrimonio').value.trim()
                                || document.getElementById('os_patrimonio_busca').value.trim();

            if (!descricao || !tipo) {
                alert('Preencha Descrição e Tipo!');
                return;
            }

            try {
                const res  = await fetch('../actions/os/criar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ descricao, tipo, patrimonio })
                });
                const data = await res.json();

                if (data.success) {
                    closeModal('novaOS');
                    document.getElementById('os_descricao').value       = '';
                    document.getElementById('os_tipo').value            = '';
                    document.getElementById('os_patrimonio').value      = '';
                    document.getElementById('os_patrimonio_busca').value = '';
                    document.getElementById('patrimonio-selecionado').style.display = 'none';
                    carregarOS();
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
                const res  = await fetch(`../actions/os/detalhe_os.php?id=${id}`);
                const data = await res.json();

                if (data.success) {
                    osAtual = data.dados;
                    const os = data.dados;

                    document.getElementById('detalhe-os-titulo').textContent   = `Ordem de Serviço: #${os.id}`;
                    document.getElementById('detalhe-os-data').textContent      = formatarData(os.criado_em);
                    document.getElementById('detalhe-os-solicitante').textContent = os.solicitante_nome;
                    document.getElementById('detalhe-os-tipo').textContent      = os.tipo;
                    document.getElementById('detalhe-os-status').innerHTML      = `<span class="os-status-badge ${getStatusClass(os.status)}">${os.status}</span>`;
                    document.getElementById('detalhe-os-patrimonio').textContent = os.patrimonio || 'Não informado';
                    document.getElementById('detalhe-os-descricao').textContent = os.descricao;
                    document.getElementById('detalhe-os-origem').textContent    = os.solicitante_nome;
                    document.getElementById('detalhe-os-destino').textContent   = os.responsavel_nome;
                    document.getElementById('detalhe-os-destino-fluxo').textContent = os.responsavel_nome;
                    document.getElementById('encaminhar_os_id').value           = os.id;

                    // Campos gasto + obs (só visíveis se Arquivada)
                    const isArquivada = os.status === 'Arquivada';
                    document.getElementById('detalhe-os-bloco-gasto').style.display = isArquivada ? '' : 'none';
                    document.getElementById('detalhe-os-bloco-obs').style.display   = isArquivada ? '' : 'none';
                    if (isArquivada) {
                        document.getElementById('detalhe-os-gasto').textContent = os.gasto ? 'R$ ' + parseFloat(os.gasto).toFixed(2).replace('.', ',') : 'Não informado';
                        document.getElementById('detalhe-os-obs').textContent   = os.obs_finalizacao || 'Não informado';
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
                    const ehAdmin       = USUARIO_PERMISSAO === 'ADMIN';
                    const podeAgir      = ehResponsavel || ehAdmin;
                    const naoArquivada  = os.status !== 'Arquivada';

                    document.getElementById('btn-encaminhar-os').style.display = (podeAgir && naoArquivada) ? '' : 'none';
                    document.getElementById('btn-recusar-os').style.display    = (podeAgir && os.status === 'Aguardando Aprovação') ? '' : 'none';

                    showModal('detalheOS');
                }
            } catch (err) {
                console.error('Erro ao carregar detalhe:', err);
            }
        }

        // ---------- ACEITAR ----------
        function confirmarAceitarOS(id) {
            document.getElementById('aceitar_os_id').value = id;
            showModal('aceitarOSModal');
        }

        async function aceitarOS() {
            const id = document.getElementById('aceitar_os_id').value;
            try {
                const res  = await fetch('../actions/os/aceitar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(id) })
                });
                const data = await res.json();

                closeModal('aceitarOSModal');
                if (data.success) {
                    trocarAba('andamento');
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao aceitar O.S.:', err);
            }
        }

        // ---------- ENCAMINHAR ----------
        async function encaminharOS() {
            const os_id          = document.getElementById('encaminhar_os_id').value || osIdSelecionada;
            const responsavel_id = document.getElementById('encaminhar_responsavel').value;
            const motivo         = document.getElementById('encaminhar_motivo').value.trim();

            if (!responsavel_id || !motivo) {
                alert('Preencha todos os campos!');
                return;
            }

            try {
                const res  = await fetch('../actions/os/encaminhar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(os_id), responsavel_id: parseInt(responsavel_id), motivo })
                });
                const data = await res.json();

                closeModal('encaminharOS');
                closeModal('detalheOS');
                document.getElementById('encaminhar_motivo').value      = '';
                document.getElementById('encaminhar_responsavel').value = '';

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
        function confirmarArquivarOS(id) {
            document.getElementById('arquivar_os_id').value = id;
            document.getElementById('arquivar_gasto').value = '';
            document.getElementById('arquivar_obs').value   = '';
            showModal('arquivarOSModal');
        }

        async function arquivarOS() {
            const id  = document.getElementById('arquivar_os_id').value;
            const gasto = document.getElementById('arquivar_gasto').value;
            const obs   = document.getElementById('arquivar_obs').value.trim();

            try {
                const res  = await fetch('../actions/os/arquivar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(id), gasto: gasto !== '' ? parseFloat(gasto) : null, obs_finalizacao: obs })
                });
                const data = await res.json();

                closeModal('arquivarOSModal');
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
            document.getElementById('recusar_os_id').value  = id;
            document.getElementById('recusar_motivo').value = '';
            showModal('recusarOSModal');
        }

        async function recusarOS() {
            const id     = document.getElementById('recusar_os_id').value;
            const motivo = document.getElementById('recusar_motivo').value.trim();

            if (!motivo) {
                alert('Informe o motivo da recusa!');
                return;
            }

            try {
                const res  = await fetch('../actions/os/recusar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(id), motivo_recusa: motivo })
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

        // ---------- HISTÓRICO ----------
        async function verHistoricoOS() {
            const id = osIdSelecionada;
            if (!id) return;

            try {
                const res  = await fetch(`../actions/os/get_historico.php?os_id=${id}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('historico-os-titulo').textContent = `Histórico da O.S.: #${id}`;
                    const timeline = document.getElementById('historico-os-timeline');

                    if (data.dados.length === 0) {
                        timeline.innerHTML = '<p style="text-align: center; color: var(--corTxt3); opacity: 0.6;">Nenhum registro no histórico</p>';
                    } else {
                        timeline.innerHTML = data.dados.map(h => {
                            const statusClass = h.status.includes('Criada')      ? 'os-hist-criada' :
                                                h.status.includes('Aceita')      ? 'os-hist-aceita' :
                                                h.status.includes('Encaminhada') ? 'os-hist-encaminhada' :
                                                h.status.includes('Arquivada')   ? 'os-hist-arquivada' :
                                                h.status.includes('Recusada')    ? 'os-hist-recusada' : '';

                            return `
                                <div class="os-timeline-item ${statusClass}">
                                    <div class="os-timeline-header">
                                        <span class="os-timeline-data">${formatarData(h.criado_em)}</span>
                                        <span class="os-timeline-status">${h.status}</span>
                                    </div>
                                    <div class="os-timeline-body">
                                        <p><strong>De:</strong> ${h.origem_nome} &nbsp;|&nbsp; <strong>Para:</strong> ${h.destino_nome}</p>
                                        <p class="os-timeline-desc">${h.descricao}</p>
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
    </script>
</body>

</html>