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

        <!-- Barra de Ações: Pesquisa + Botão Nova O.S. -->
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

        <!-- Cards de Resumo -->
        <div class="os-resumo-container">
            <div class="os-resumo-card os-resumo-aberto os-tab-ativo" onclick="filtrarOS('Em Aberto')">
                <div class="os-resumo-label">Principais (Aberto/Aceitas)</div>
                <div class="os-resumo-numero" id="contador-aberto">0</div>
            </div>
            <div class="os-resumo-card os-resumo-aguardando" onclick="filtrarOS('Aguardando Aprovação')">
                <div class="os-resumo-label">Encaminhadas (Privadas)</div>
                <div class="os-resumo-numero" id="contador-aguardando">0</div>
            </div>
            <div class="os-resumo-card os-resumo-arquivada" onclick="filtrarOS('Arquivada')">
                <div class="os-resumo-label">Arquivadas</div>
                <div class="os-resumo-numero" id="contador-arquivada">0</div>
            </div>
            <div class="os-resumo-card os-resumo-todas" onclick="filtrarOS('')">
                <div class="os-resumo-label">Todas Visíveis</div>
                <div class="os-resumo-numero" id="contador-todas">0</div>
            </div>
        </div>

        <!-- Tabela de O.S. -->
        <div class="tabela-bg2" style="height: 55vh;">
            <div class="tabela-titulo">
                <i class="bi bi-file-earmark-text"></i>
                <h2 id="os-titulo-tabela">Ordens de Serviço — Em Aberto</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Nº O.S.</th>
                            <th style="width: 30%;">DESCRIÇÃO</th>
                            <th style="width: 12%;">TIPO</th>
                            <th style="width: 15%;">SOLICITANTE</th>
                            <th style="width: 15%;">RESPONSÁVEL</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 10%; text-align: center;">AÇÕES</th>
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
        let filtroAtual = 'Em Aberto';
        let osIdSelecionada = null;

        document.addEventListener('DOMContentLoaded', () => {
            carregarOS();
        });

        // ---------- CARREGAR / LISTAR ----------
        async function carregarOS() {
            const busca = document.getElementById('pesquisa-os')?.value || '';
            const params = new URLSearchParams();
            if (filtroAtual) params.append('status', filtroAtual);
            if (busca) params.append('search', busca);

            try {
                const res = await fetch(`../actions/os/listar_os.php?${params.toString()}`);
                const data = await res.json();

                if (data.success) {
                    renderizarTabela(data.dados);
                    atualizarContadores(data.contadores, data.dados.length);
                }
            } catch (err) {
                console.error('Erro ao carregar O.S.:', err);
            }
        }

        function renderizarTabela(ordens) {
            const tbody = document.getElementById('tabela-os-body');

            if (ordens.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--corTxt3); opacity: 0.6;">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p style="margin-top: 10px;">Nenhuma O.S. encontrada</p>
                        </td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = ordens.map(os => {
                const statusClass = getStatusClass(os.status);
                const dataFormatada = formatarData(os.criado_em);
                const descResumida = os.descricao.length > 60 ? os.descricao.substring(0, 60) + '...' : os.descricao;

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
                                <button class="btnAcao editar" title="Ver Detalhes" onclick="verDetalheOS(${os.id})">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                ${os.status === 'Em Aberto' || os.status === 'Aguardando Aprovação' ? `
                                    <button class="btnAcao confirmar" title="Aceitar" onclick="confirmarAceitarOS(${os.id})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                ` : ''}
                                ${os.status !== 'Arquivada' ? `
                                    <button class="btnAcao deletar" title="Arquivar" onclick="confirmarArquivarOS(${os.id})">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        function atualizarContadores(contadores, totalAtual) {
            document.getElementById('contador-aberto').textContent = parseInt(contadores.em_aberto) + parseInt(contadores.aceita || 0);
            document.getElementById('contador-arquivada').textContent = contadores.arquivada;
            document.getElementById('contador-aguardando').textContent = contadores.aguardando;
            document.getElementById('contador-todas').textContent = parseInt(contadores.em_aberto) + parseInt(contadores.arquivada) + parseInt(contadores.aguardando) + parseInt(contadores.aceita || 0);
        }

        function getStatusClass(status) {
            const map = {
                'Em Aberto': 'os-status-aberto',
                'Aguardando Aprovação': 'os-status-aguardando',
                'Aceita': 'os-status-aceita',
                'Arquivada': 'os-status-arquivada'
            };
            return map[status] || 'os-status-aberto';
        }

        function formatarData(dataStr) {
            if (!dataStr) return '';
            const d = new Date(dataStr);
            return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        }

        // ---------- FILTRAR ----------
        function filtrarOS(status) {
            filtroAtual = status;

            // Atualizar visual das tabs
            document.querySelectorAll('.os-resumo-card').forEach(c => c.classList.remove('os-tab-ativo'));
            if (status === 'Em Aberto') document.querySelector('.os-resumo-aberto')?.classList.add('os-tab-ativo');
            else if (status === 'Arquivada') document.querySelector('.os-resumo-arquivada')?.classList.add('os-tab-ativo');
            else if (status === 'Aguardando Aprovação') document.querySelector('.os-resumo-aguardando')?.classList.add('os-tab-ativo');
            else document.querySelector('.os-resumo-todas')?.classList.add('os-tab-ativo');

            // Atualizar título
            const titulos = {
                'Em Aberto': 'Em Aberto',
                'Arquivada': 'Arquivadas',
                'Aguardando Aprovação': 'Aguardando Aprovação',
                '': 'Todas'
            };
            document.getElementById('os-titulo-tabela').textContent = `Status: ${status === 'Em Aberto' ? 'Aberto / Aceitas' : (status === 'Aguardando Aprovação' ? 'Encaminhadas (Privadas)' : (titulos[status] || 'Todas'))}`;

            carregarOS();
        }

        // ---------- CRIAR O.S. ----------
        async function criarOS() {
            const descricao = document.getElementById('os_descricao').value.trim();
            const tipo = document.getElementById('os_tipo').value;
            const responsavel_id = document.getElementById('os_responsavel').value;

            if (!descricao || !tipo || !responsavel_id) {
                alert('Preencha todos os campos!');
                return;
            }

            try {
                const res = await fetch('../actions/os/criar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ descricao, tipo, responsavel_id: parseInt(responsavel_id) })
                });
                const data = await res.json();

                if (data.success) {
                    closeModal('novaOS');
                    document.getElementById('os_descricao').value = '';
                    document.getElementById('os_tipo').value = '';
                    document.getElementById('os_responsavel').value = '';
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
                const res = await fetch(`../actions/os/detalhe_os.php?id=${id}`);
                const data = await res.json();

                if (data.success) {
                    const os = data.dados;
                    document.getElementById('detalhe-os-titulo').textContent = `Ordem de Serviço: ${os.id}`;
                    document.getElementById('detalhe-os-data').textContent = formatarData(os.criado_em);
                    document.getElementById('detalhe-os-solicitante').textContent = os.solicitante_nome;
                    document.getElementById('detalhe-os-tipo').textContent = os.tipo;
                    document.getElementById('detalhe-os-status').innerHTML = `<span class="os-status-badge ${getStatusClass(os.status)}">${os.status}</span>`;
                    document.getElementById('detalhe-os-descricao').textContent = os.descricao;
                    document.getElementById('detalhe-os-origem').textContent = os.solicitante_nome;
                    document.getElementById('detalhe-os-destino').textContent = os.responsavel_nome;
                    document.getElementById('encaminhar_os_id').value = os.id;

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
                const res = await fetch('../actions/os/aceitar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(id) })
                });
                const data = await res.json();

                closeModal('aceitarOSModal');
                if (data.success) {
                    carregarOS();
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
            const os_id = document.getElementById('encaminhar_os_id').value || osIdSelecionada;
            const responsavel_id = document.getElementById('encaminhar_responsavel').value;
            const motivo = document.getElementById('encaminhar_motivo').value.trim();

            if (!responsavel_id || !motivo) {
                alert('Preencha todos os campos!');
                return;
            }

            try {
                const res = await fetch('../actions/os/encaminhar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(os_id), responsavel_id: parseInt(responsavel_id), motivo })
                });
                const data = await res.json();

                closeModal('encaminharOS');
                closeModal('detalheOS');
                document.getElementById('encaminhar_motivo').value = '';
                document.getElementById('encaminhar_responsavel').value = '';

                if (data.success) {
<<<<<<< HEAD
                    filtrarOS('Aguardando Aprovação'); // Redireciona para aba privada para mostrar que a OS está lá
                    alert(data.message);
=======
                    carregarOS();
                    exibirSucesso(data.message);
>>>>>>> e70253c17b1bf3a75a010f2ba6a0d96a3c9509cb
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
            showModal('arquivarOSModal');
        }

        async function arquivarOS() {
            const id = document.getElementById('arquivar_os_id').value;
            try {
                const res = await fetch('../actions/os/arquivar_os.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ os_id: parseInt(id) })
                });
                const data = await res.json();

                closeModal('arquivarOSModal');
                if (data.success) {
                    carregarOS();
                    exibirSucesso(data.message);
                } else {
                    alert(data.message);
                }
            } catch (err) {
                console.error('Erro ao arquivar O.S.:', err);
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
                    document.getElementById('historico-os-titulo').textContent = `Histórico da Ordem de Serviço: ${id}`;
                    const timeline = document.getElementById('historico-os-timeline');

                    if (data.dados.length === 0) {
                        timeline.innerHTML = '<p style="text-align: center; color: var(--corTxt3); opacity: 0.6;">Nenhum registro no histórico</p>';
                    } else {
                        timeline.innerHTML = data.dados.map(h => {
                            const dataFormatada = formatarData(h.criado_em);
                            const statusClass = h.status.includes('Criada') ? 'os-hist-criada' :
                                h.status.includes('Aceita') ? 'os-hist-aceita' :
                                    h.status.includes('Encaminhada') ? 'os-hist-encaminhada' :
                                        h.status.includes('Arquivada') ? 'os-hist-arquivada' : '';

                            return `
                                <div class="os-timeline-item ${statusClass}">
                                    <div class="os-timeline-header">
                                        <span class="os-timeline-data">${dataFormatada}</span>
                                        <span class="os-timeline-status">Status Histórico: ${h.status}</span>
                                    </div>
                                    <div class="os-timeline-body">
                                        <p><strong>Origem:</strong> ${h.origem_nome} &nbsp;|&nbsp; <strong>Destino:</strong> ${h.destino_nome}</p>
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