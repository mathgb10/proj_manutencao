<!-- =============================================
     MODAIS DO SISTEMA DE ORDEM DE SERVIÇO
     ============================================= -->

<!-- Modal: Nova Ordem de Serviço -->
<div class="modal-fundo" id="novaOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Nova Ordem de Serviço</h3>
            <button class="" onclick="closeModal('novaOS')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form">
            <div class="modal-input">
                <label for="os_descricao">Descrição:</label>
                <div class="input-wrapper">
                    <textarea id="os_descricao" placeholder="Descreva a solicitação..." rows="3"
                        style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);resize:vertical;font-family:inherit;"></textarea>
                </div>
            </div>

            <div class="modal-row">
                <div class="modal-input">
                    <label for="os_tipo">Tipo:</label>
                    <div class="input-wrapper">
                        <select id="os_tipo"
                            style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);cursor:pointer;">
                            <option value="" disabled selected>Selecione o tipo</option>
                            <option value="Manutenção">Manutenção</option>
                            <option value="Patrimônio">Patrimônio</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                </div>

                <div class="modal-input">
                    <label for="os_responsavel">Encaminhar para:</label>
                    <div class="input-wrapper">
                        <select id="os_responsavel"
                            style="width:100%;padding:10px;border:1px solid var(--corBordas);border-radius:8px;background:var(--corFundo);color:var(--corTxt3);cursor:pointer;">
                            <option value="" disabled selected>Selecione o responsável</option>
                            <?php
                            $sqlUsers = "SELECT id, nome FROM usuarios ORDER BY nome ASC";
                            $resUsers = $conn->query($sqlUsers);
                            if ($resUsers && $resUsers->num_rows > 0) {
                                while ($user = $resUsers->fetch_assoc()) {
                                    echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['nome']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-confirmar-full confirmar" onclick="criarOS()">
                    Criar O.S. <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detalhe da O.S. -->
<div class="modal-fundo" id="detalheOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="detalhe-os-titulo">Ordem de Serviço: #</h3>
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
                <div class="os-info-item" style="grid-column: 1 / -1;">
                    <strong>Descrição:</strong>
                    <p id="detalhe-os-descricao" style="margin-top:5px;"></p>
                </div>
            </div>

            <!-- Seção Anexos -->
            <div class="os-detalhe-anexos">
                <strong><i class="bi bi-paperclip"></i> Anexos:</strong>
                <div id="detalhe-os-anexos-lista" style="margin-top:8px;"></div>
            </div>

            <!-- Seção Origem → Destino -->
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
                    <span class="os-fluxo-label">Destino</span>
                    <div class="os-fluxo-avatar">
                        <i class="bi bi-person-circle"></i>
                        <span id="detalhe-os-destino"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="gap: 10px;">
                <button type="button" class="btn-confirmar-full editar" onclick="verHistoricoOS()"
                    id="btn-historico-os">
                    <i class="bi bi-clock-history"></i> Histórico
                </button>
                <button type="button" class="btn-confirmar-full confirmar" onclick="showModal('encaminharOS')"
                    id="btn-encaminhar-os">
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
                <!-- Timeline carregada via JS -->
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-confirmar-full editar" onclick="closeModal('historicoOS')">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
        </div>
    </div>
</div>

<!-- Modal: Encaminhar O.S. -->
<div class="modal-fundo" id="encaminharOS" style="display: none">
    <div class="modal-box modal-box-wide" style="max-width: 550px;">
        <div class="modal-header">
            <h3>Encaminhar O.S.</h3>
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
                        $resUsers2 = $conn->query($sqlUsers);
                        if ($resUsers2 && $resUsers2->num_rows > 0) {
                            while ($user2 = $resUsers2->fetch_assoc()) {
                                echo "<option value='" . $user2['id'] . "'>" . htmlspecialchars($user2['nome']) . "</option>";
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

            <div class="modal-footer">
                <button type="button" class="btn-confirmar-full confirmar" onclick="encaminharOS()">
                    Encaminhar <i class="bi bi-send"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmação para Aceitar -->
<div class="modal-fundo" id="aceitarOSModal" style="display: none;">
    <div class="modal-box" style="width: 400px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3>Aceitar O.S.</h3>
            <button onclick="closeModal('aceitarOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que deseja aceitar esta Ordem de Serviço?</p>
        </div>
        <input type="hidden" id="aceitar_os_id">
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <button onclick="aceitarOS()" class="btn-confirmar-full confirmar">Sim, Aceitar</button>
            <button onclick="closeModal('aceitarOSModal')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Cancelar</button>
        </div>
    </div>
</div>

<!-- Modal: Confirmação para Arquivar -->
<div class="modal-fundo" id="arquivarOSModal" style="display: none;">
    <div class="modal-box" style="width: 400px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3>Arquivar O.S.</h3>
            <button onclick="closeModal('arquivarOSModal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que deseja arquivar esta Ordem de Serviço?</p>
        </div>
        <input type="hidden" id="arquivar_os_id">
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <button onclick="arquivarOS()" class="btn-confirmar-full confirmar">Sim, Arquivar</button>
            <button onclick="closeModal('arquivarOSModal')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Cancelar</button>
        </div>
    </div>
</div>