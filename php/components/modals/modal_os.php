<!-- =============================================
     MODAIS DO SISTEMA DE ORDEM DE SERVIÇO
     ============================================= -->

<!-- Modal: Nova Ordem de Serviço -->
<div class="modal-fundo" id="novaOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 620px;">
        <div class="modal-header">
            <h3><i class="bi bi-file-earmark-plus"></i> Nova Ordem de Serviço</h3>
            <div style="display:flex;gap:8px;align-items:center;">
                <button type="button" onclick="abrirModalMaquinas()" style="
                    display:flex;align-items:center;gap:6px;
                    padding:7px 14px;border-radius:8px;border:none;cursor:pointer;
                    background:var(--editar);color:#fff;font-size:.85rem;font-weight:600;">
                    <i class="bi bi-search"></i> Pesquisar Máquina
                </button>
                <button onclick="closeModal('novaOS')"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>

        <div class="modal-form">

            <!-- Descrição -->
            <div class="modal-input">
                <label for="os_descricao">Descrição do Problema:</label>
                <div class="input-wrapper">
                    <textarea id="os_descricao" placeholder="Descreva o problema detalhadamente..." rows="3"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <!-- Tipo -->
            <div class="modal-input">
                <label for="os_tipo">Tipo:</label>
                <div class="input-wrapper">
                    <select id="os_tipo"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);cursor:pointer;">
                        <option value="" disabled selected>Selecione o tipo</option>
                        <option value="Corretivo">Corretivo</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>
            </div>

            <!-- Máquina / Patrimônio selecionado -->
            <div class="modal-input">
                <label>Patrimônio / Máquina:</label>
                <div id="maquina-selecionada-box" onclick="abrirModalMaquinas()" style="
                    display:flex;align-items:center;gap:10px;
                    padding:11px 14px;border:1.5px dashed var(--corBordas);
                    border-radius:8px;background:var(--corFundo);cursor:pointer;
                    transition:.2s;"
                    onmouseenter="this.style.borderColor='var(--corBase)'"
                    onmouseleave="this.style.borderColor='var(--corBordas)'">
                    <i class="bi bi-cpu" style="color:var(--corBase);font-size:1.2rem;flex-shrink:0;"></i>
                    <span id="maquina-selecionada-texto" style="color:var(--corTxt3);opacity:.55;font-size:.9rem;">
                        Clique para selecionar uma máquina...
                    </span>
                </div>
                <input type="hidden" id="os_patrimonio">
                <input type="hidden" id="os_maquina_id">
            </div>

            <!-- Anexo -->
            <div class="modal-input">
                <label>Anexo (Opcional):</label>
                
                <!-- Área de Clique para Upload -->
                <div id="anexo-upload-container" onclick="document.getElementById('os_anexo').click()" style="
                    display: flex; flex-direction: column; align-items: center; justify-content: center;
                    padding: 20px; border: 1.5px dashed var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); cursor: pointer; transition: all 0.2s ease-in-out;
                    text-align: center; gap: 8px;"
                    onmouseenter="this.style.borderColor='var(--corBase)'; this.style.background='rgba(252,35,35,0.03)';"
                    onmouseleave="this.style.borderColor='var(--corBordas)'; this.style.background='var(--corFundo)';"
                >
                    <i class="bi bi-cloud-arrow-up" style="font-size: 1.8rem; color: var(--corBase);"></i>
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--corTxt3);">Clique para fazer upload de um arquivo</span>
                    <span style="font-size: 0.75rem; color: var(--corTxt3); opacity: 0.6;">JPG, PNG ou PDF (Máx. 5MB)</span>
                </div>

                <!-- Input oculto -->
                <input type="file" id="os_anexo" name="anexo" accept=".jpg,.jpeg,.png,.pdf" onchange="atualizarFeedbackAnexo(this)" style="display: none;">

                <!-- Área de visualização do arquivo selecionado -->
                <div id="anexo-selecionado-box" style="
                    display: none; align-items: center; justify-content: space-between;
                    padding: 12px 16px; border: 1.5px solid var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); margin-top: 8px;"
                >
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                        <i id="anexo-icon" class="bi bi-file-earmark" style="font-size: 1.3rem; color: var(--corBase); flex-shrink: 0;"></i>
                        <span id="anexo-nome-arquivo" style="font-size: 0.85rem; font-weight: 500; color: var(--corTxt3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; flex: 1;">
                            Nome do arquivo.pdf
                        </span>
                    </div>
                    <button type="button" onclick="removerAnexoSelecionado()" style="
                        background: none; border: none; padding: 4px; cursor: pointer;
                        color: var(--corBase); transition: opacity 0.2s; display: flex; align-items: center; justify-content: center;"
                        onmouseenter="this.style.opacity='0.7'"
                        onmouseleave="this.style.opacity='1'"
                    >
                        <i class="bi bi-x-circle-fill" style="font-size: 1.2rem;"></i>
                    </button>
                </div>
            </div>

            <div class="modal-footer" style="margin-top:12px;">
                <button type="button" class="btn-confirmar-full confirmar" onclick="criarOS()">
                    Abrir O.S. <i class="bi bi-send"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============================================
     SUB-MODAL: Pesquisar Máquina
     ============================================= -->
<div class="modal-fundo" id="modalPesquisaMaquina" style="display:none; z-index: 10000;">
    <div class="modal-box modal-box-wide" style="max-width:650px;">
        <div class="modal-header">
            <h3><i class="bi bi-cpu"></i> Selecionar Máquina</h3>
            <button onclick="closeModal('modalPesquisaMaquina')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form" style="padding-bottom:0;">
            <!-- Busca -->
            <div style="display:flex;gap:8px;margin-bottom:14px;">
                <div style="flex:1;position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--corTxt3);opacity:.5;"></i>
                    <input type="text" id="maquina-modal-busca"
                        placeholder="Buscar por nome, NI ou setor..."
                        oninput="filtrarMaquinasModal(this.value)"
                        style="width:100%;padding:10px 10px 10px 36px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);font-family:inherit;box-sizing:border-box;">
                </div>
                <button type="button" onclick="limparBuscaMaquinasModal()" style="
                    padding:10px 14px;border-radius:8px;border:1px solid var(--corBordas);
                    background:var(--corFundo2);color:var(--corTxt3);cursor:pointer;font-size:.85rem;">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

            <!-- Lista de máquinas -->
            <div id="maquina-modal-lista" style="
                max-height:380px;overflow-y:auto;
                display:flex;flex-direction:column;gap:6px;
                padding-bottom:16px;">
                <div style="text-align:center;padding:30px;opacity:.5;">
                    <i class="bi bi-hourglass-split" style="font-size:2rem;"></i>
                    <p style="margin-top:8px;">Carregando máquinas...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detalhe da O.S. -->
<div class="modal-fundo" id="detalheOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="detalhe-os-titulo" style="margin-bottom: 0;">Ordem de Serviço: #</h3>
            <button class="" onclick="closeModal('detalheOS')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form">
            <div class="os-detalhe-info">
                <div class="os-info-item">
                    <strong>Data Abertura:</strong>
                    <span id="detalhe-os-data"></span>
                </div>
                <div class="os-info-item">
                    <strong>Criado por:</strong>
                    <span id="detalhe-os-solicitante"></span>
                </div>
                <div class="os-info-item">
                    <strong>Tipo:</strong>
                    <span id="detalhe-os-tipo"></span>
                </div>
                <div class="os-info-item">
                    <strong>Status:</strong>
                    <span id="detalhe-os-status"></span>
                </div>
                <div class="os-info-item">
                    <strong>Patrimônio:</strong>
                    <span id="detalhe-os-patrimonio"></span>
                </div>
                <div class="os-info-item">
                    <strong>Responsável Atual:</strong>
                    <span id="detalhe-os-destino"></span>
                </div>
                <div class="os-info-item" style="grid-column: 1 / -1;">
                    <strong>Descrição:</strong>
                    <p id="detalhe-os-descricao" style="margin-top:5px;"></p>
                </div>
                <div class="os-info-item os-info-arquivada" id="detalhe-os-bloco-gasto" style="display:none;">
                    <strong>Gasto (R$):</strong>
                    <span id="detalhe-os-gasto"></span>
                </div>
                <div class="os-info-item os-info-arquivada" style="grid-column: 1 / -1; display:none;" id="detalhe-os-bloco-obs">
                    <strong>Obs. Finalização:</strong>
                    <p id="detalhe-os-obs" style="margin-top:5px;"></p>
                </div>
            </div>

            <!-- Anexos -->
            <div class="os-detalhe-anexos">
                <strong><i class="bi bi-paperclip"></i> Anexos:</strong>
                <div id="detalhe-os-anexos-lista" style="margin-top:8px;"></div>
            </div>

            <!-- Fluxo Visual -->
            <div class="os-fluxo-visual">
                <div class="os-fluxo-item">
                    <span class="os-fluxo-label">Origem</span>
                    <div class="os-fluxo-avatar">
                        <i class="bi bi-person-circle"></i>
                        <span id="detalhe-os-origem"></span>
                    </div>
                </div>
                <i class="bi bi-arrow-right os-fluxo-seta"></i>
                <div class="os-fluxo-item">
                    <span class="os-fluxo-label">Destino Atual</span>
                    <div class="os-fluxo-avatar">
                        <i class="bi bi-person-circle"></i>
                        <span id="detalhe-os-destino-fluxo"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="display: flex; justify-content: flex-start; align-items: center; gap: 10px;">
                <button type="button" class="os-btn-premium os-btn-historico" onclick="verHistoricoOS()" id="btn-historico-os">
                    <i class="bi bi-clock-history"></i> Histórico
                </button>
                <button type="button" class="os-btn-premium os-btn-observacao" onclick="abrirModalObservacao()" id="btn-observacao-os" style="background-color: #607d8b;">
                    <i class="bi bi-chat-left-text"></i> Observação
                </button>
                <button type="button" class="os-btn-premium os-btn-encaminhar" onclick="showModal('encaminharOS')" id="btn-encaminhar-os" style="display:none;">
                    <i class="bi bi-send"></i> Encaminhar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Histórico -->
<div class="modal-fundo" id="historicoOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 750px;">
        <div class="modal-header">
            <h3 id="historico-os-titulo">Histórico da Ordem de Serviço: #</h3>
            <button class="" onclick="closeModal('historicoOS')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form" style="max-height: 60vh; overflow-y: auto;">
            <div id="historico-os-timeline" class="os-timeline">
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="os-btn-premium os-btn-voltar" onclick="closeModal('historicoOS')">
                <i class="bi bi-arrow-left"></i> Voltar para Detalhes
            </button>
        </div>
    </div>
</div>

<!-- Modal: Encaminhar O.S. -->
<div class="modal-fundo" id="encaminharOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 550px;">
        <div class="modal-header">
            <h3><i class="bi bi-send"></i> Encaminhar O.S.</h3>
            <button class="" onclick="closeModal('encaminharOS')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form">
            <input type="hidden" id="encaminhar_os_id">

            <div class="modal-input">
                <label for="encaminhar_responsavel">Encaminhar para:</label>
                <div class="input-wrapper">
                    <select id="encaminhar_responsavel"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);cursor:pointer;">
                        <option value="" disabled selected>Selecione o responsável</option>
                        <?php
                        $sqlUsers = "SELECT id, nome, permissao FROM usuarios ORDER BY nome ASC";
                        $resUsers2 = $conn->query($sqlUsers);
                        if ($resUsers2 && $resUsers2->num_rows > 0) {
                            while ($user2 = $resUsers2->fetch_assoc()) {
                                $cargo = $user2['permissao'] !== 'NORMAL' ? ' (' . $user2['permissao'] . ')' : '';
                                echo "<option value='" . $user2['id'] . "'>" . htmlspecialchars($user2['nome']) . $cargo . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="modal-input">
                <label for="encaminhar_motivo">Motivo:</label>
                <div class="input-wrapper">
                    <textarea id="encaminhar_motivo" placeholder="Descreva o motivo do encaminhamento..." rows="3"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <!-- Anexo do Encaminhamento -->
            <div class="modal-input" style="margin-bottom: 14px;">
                <label>Anexo (Opcional):</label>
                
                <!-- Área de Clique para Upload -->
                <div id="enc-anexo-upload-container" onclick="document.getElementById('enc_anexo').click()" style="
                    display: flex; flex-direction: column; align-items: center; justify-content: center;
                    padding: 16px; border: 1.5px dashed var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); cursor: pointer; transition: all 0.2s ease-in-out;
                    text-align: center; gap: 6px;"
                    onmouseenter="this.style.borderColor='var(--corBase)'; this.style.background='rgba(252,35,35,0.03)';"
                    onmouseleave="this.style.borderColor='var(--corBordas)'; this.style.background='var(--corFundo)';"
                >
                    <i class="bi bi-cloud-arrow-up" style="font-size: 1.6rem; color: var(--corBase);"></i>
                    <span style="font-size: 0.8rem; font-weight: 500; color: var(--corTxt3);">Clique para anexar um arquivo</span>
                    <span style="font-size: 0.7rem; color: var(--corTxt3); opacity: 0.6;">JPG, PNG ou PDF (Máx. 5MB)</span>
                </div>

                <!-- Input oculto -->
                <input type="file" id="enc_anexo" name="anexo" accept=".jpg,.jpeg,.png,.pdf" onchange="atualizarFeedbackEncAnexo(this)" style="display: none;">

                <!-- Área de visualização do arquivo selecionado -->
                <div id="enc-anexo-selecionado-box" style="
                    display: none; align-items: center; justify-content: space-between;
                    padding: 10px 14px; border: 1.5px solid var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); margin-top: 8px;"
                >
                    <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;">
                        <i id="enc-anexo-icon" class="bi bi-file-earmark" style="font-size: 1.2rem; color: var(--corBase); flex-shrink: 0;"></i>
                        <span id="enc-anexo-nome-arquivo" style="font-size: 0.8rem; font-weight: 500; color: var(--corTxt3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; flex: 1;">
                            Nome do arquivo.pdf
                        </span>
                    </div>
                    <button type="button" onclick="removerEncAnexoSelecionado()" style="
                        background: none; border: none; padding: 4px; cursor: pointer;
                        color: var(--corBase); transition: opacity 0.2s; display: flex; align-items: center; justify-content: center;"
                        onmouseenter="this.style.opacity='0.7'"
                        onmouseleave="this.style.opacity='1'"
                    >
                        <i class="bi bi-x-circle-fill" style="font-size: 1.1rem;"></i>
                    </button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="os-btn-premium os-btn-encaminhar" onclick="encaminharOS()">
                    Confirmar Encaminhamento <i class="bi bi-send-check"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Aceitar O.S. -->
<div class="modal-fundo" id="aceitarOSModal" style="display: none;">
    <div class="modal-box" style="width: 400px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3><i class="bi bi-check-circle"></i> Aceitar O.S.</h3>
            <button onclick="closeModal('aceitarOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que deseja aceitar esta Ordem de Serviço?<br>
            <small style="opacity:.7;">Você será o responsável por executá-la.</small></p>
        </div>
        <input type="hidden" id="aceitar_os_id">
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <button onclick="aceitarOS()" class="btn-confirmar-full confirmar">Sim, Aceitar</button>
            <button onclick="closeModal('aceitarOSModal')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Cancelar</button>
        </div>
    </div>
</div>

<!-- Modal: Arquivar (Finalizar) O.S. -->
<div class="modal-fundo" id="arquivarOSModal" style="display: none;">
    <div class="modal-box modal-box-wide" style="max-width: 500px;">
        <div class="modal-header" style="margin-bottom: 16px;">
            <h3><i class="bi bi-archive"></i> Finalizar / Arquivar O.S.</h3>
            <button onclick="closeModal('arquivarOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-form">
            <input type="hidden" id="arquivar_os_id">

            <div class="modal-input">
                <label for="arquivar_gasto">Gasto Total (R$):</label>
                <div class="input-wrapper">
                    <input type="number" id="arquivar_gasto" placeholder="Ex: 150.00" min="0" step="0.01"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);font-family:inherit;">
                </div>
            </div>

            <div class="modal-input">
                <label for="arquivar_obs">Observação de Finalização:</label>
                <div class="input-wrapper">
                    <textarea id="arquivar_obs" placeholder="Descreva o que foi feito, peças utilizadas, etc..." rows="4"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <div class="modal-footer" style="gap: 10px; display:flex;">
                <button onclick="arquivarOS()" class="btn-confirmar-full confirmar">
                    <i class="bi bi-archive-fill"></i> Finalizar O.S.
                </button>
                <button onclick="closeModal('arquivarOSModal')" type="button" class="btn-confirmar-full confirmar"
                    style="background-color:var(--corBase);">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Recusar O.S. -->
<div class="modal-fundo" id="recusarOSModal" style="display: none;">
    <div class="modal-box modal-box-wide" style="max-width: 500px;">
        <div class="modal-header" style="margin-bottom: 16px;">
            <h3><i class="bi bi-x-circle"></i> Recusar O.S.</h3>
            <button onclick="closeModal('recusarOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-form">
            <input type="hidden" id="recusar_os_id">

            <div style="background: rgba(231,76,60,.1); border:1px solid rgba(231,76,60,.3); border-radius:8px; padding:12px; margin-bottom:14px; color:var(--corTxt3);">
                <i class="bi bi-exclamation-triangle" style="color:#e74c3c;"></i>
                A O.S. será devolvida para o responsável anterior.
            </div>

            <div class="modal-input">
                <label for="recusar_motivo">Motivo da Recusa:</label>
                <div class="input-wrapper">
                    <textarea id="recusar_motivo" placeholder="Explique o motivo da recusa..." rows="3"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <div class="modal-footer" style="gap: 10px; display:flex;">
                <button onclick="recusarOS()" class="btn-confirmar-full confirmar" style="background:#e74c3c;">
                    <i class="bi bi-x-circle-fill"></i> Confirmar Recusa
                </button>
                <button onclick="closeModal('recusarOSModal')" type="button" class="btn-confirmar-full confirmar"
                    style="background-color:var(--corBase);">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Adicionar Observação Avulsa -->
<div class="modal-fundo" id="observacaoOSModal" style="display: none;">
    <div class="modal-box modal-box-wide" style="max-width: 500px;">
        <div class="modal-header" style="margin-bottom: 16px;">
            <h3><i class="bi bi-chat-left-text"></i> Adicionar Observação</h3>
            <button onclick="closeModal('observacaoOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-form">
            <input type="hidden" id="obs_os_id">
            
            <div class="modal-input" style="margin-bottom: 14px;">
                <label for="campo_observacao">Sua Anotação:</label>
                <div class="input-wrapper">
                    <textarea id="campo_observacao" placeholder="Digite aqui sua observação sobre esta O.S...." rows="4"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <!-- Anexo da Observação -->
            <div class="modal-input" style="margin-bottom: 14px;">
                <label>Anexo (Opcional):</label>
                
                <!-- Área de Clique para Upload -->
                <div id="obs-anexo-upload-container" onclick="document.getElementById('obs_anexo').click()" style="
                    display: flex; flex-direction: column; align-items: center; justify-content: center;
                    padding: 16px; border: 1.5px dashed var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); cursor: pointer; transition: all 0.2s ease-in-out;
                    text-align: center; gap: 6px;"
                    onmouseenter="this.style.borderColor='var(--corBase)'; this.style.background='rgba(252,35,35,0.03)';"
                    onmouseleave="this.style.borderColor='var(--corBordas)'; this.style.background='var(--corFundo)';"
                >
                    <i class="bi bi-cloud-arrow-up" style="font-size: 1.6rem; color: var(--corBase);"></i>
                    <span style="font-size: 0.8rem; font-weight: 500; color: var(--corTxt3);">Clique para anexar um arquivo</span>
                    <span style="font-size: 0.7rem; color: var(--corTxt3); opacity: 0.6;">JPG, PNG ou PDF (Máx. 5MB)</span>
                </div>

                <!-- Input oculto -->
                <input type="file" id="obs_anexo" name="anexo" accept=".jpg,.jpeg,.png,.pdf" onchange="atualizarFeedbackObsAnexo(this)" style="display: none;">

                <!-- Área de visualização do arquivo selecionado -->
                <div id="obs-anexo-selecionado-box" style="
                    display: none; align-items: center; justify-content: space-between;
                    padding: 10px 14px; border: 1.5px solid var(--corBordas); border-radius: 10px;
                    background: var(--corFundo); margin-top: 8px;"
                >
                    <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;">
                        <i id="obs-anexo-icon" class="bi bi-file-earmark" style="font-size: 1.2rem; color: var(--corBase); flex-shrink: 0;"></i>
                        <span id="obs-anexo-nome-arquivo" style="font-size: 0.8rem; font-weight: 500; color: var(--corTxt3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; flex: 1;">
                            Nome do arquivo.pdf
                        </span>
                    </div>
                    <button type="button" onclick="removerObsAnexoSelecionado()" style="
                        background: none; border: none; padding: 4px; cursor: pointer;
                        color: var(--corBase); transition: opacity 0.2s; display: flex; align-items: center; justify-content: center;"
                        onmouseenter="this.style.opacity='0.7'"
                        onmouseleave="this.style.opacity='1'"
                    >
                        <i class="bi bi-x-circle-fill" style="font-size: 1.1rem;"></i>
                    </button>
                </div>
            </div>

            <div class="modal-footer" style="gap: 10px; display:flex; margin-top: 16px;">
                <button onclick="salvarObservacao()" class="btn-confirmar-full confirmar" style="background:#607d8b;">
                    <i class="bi bi-save"></i> Salvar Observação
                </button>
                <button onclick="closeModal('observacaoOSModal')" type="button" class="btn-confirmar-full confirmar"
                    style="background-color:var(--corBase);">Cancelar</button>
            </div>
        </div>
    </div>
</div>
