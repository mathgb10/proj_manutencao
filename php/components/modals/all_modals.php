<?php if (isset($_GET['acesso']) && $_GET['acesso'] == 'negado') {
    ?>
    <div class="modal-fundo" id="acesso" style="display: flex;">
        <div class="modal-box-acesso">
            <div class="modal-header-acesso">
                <h3>Acesso Negado</h3>
                <button onclick="closeModal('acesso')"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-txt-acesso">
                <p>O seu nível de permissão não permite que você acesse está página.</p>
            </div>
            <div class="modal-btn-acesso">
                <button onclick="closeModal('acesso')" class="btn">OK</button>
            </div>
        </div>
    </div>
<?php } ?>

<div class="modal-fundo" id='notificacao-modal' style="display: none">
    <div class="modal-notificacao">
        <div class="modal-header">
            <h3 id="notif-texto">Notificações</h3>
            <button id="fechar-modal" onclick="closeModal('notificacao-modal')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-notificacao-corpo">
            <div id="notificacoes-lista">
                <!-- As notificações serão inseridas aqui dinamicamente -->
            </div>

        </div>
    </div>
</div>

<div class="modal-fundo" id="adicaoUser" style="display: none">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Registrar</h3>
            <button class="" onclick="closeModal('adicaoUser')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/user/register_user.php" class="modal-form" method="POST">

            <div class="modal-input">
                <label for="nome">Nome:</label>
                <div class="input-wrapper">
                    <input type="text" name="nome" id="nome" placeholder="Ex: Matheus dos Ateus">
                </div>
            </div>

            <div class="modal-row">
                <div class="modal-input">
                    <label for="email">E-mail:</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" placeholder="exemplo@email.com">
                    </div>
                </div>

                <div class="modal-input">
                    <label for="senha">Senha:</label>
                    <!-- coloquei a senha reandoly pois a senha é padrão então tanto faz se coloca ou não, dessa forma fica bonito :> -->
                    <div class="input-wrapper input-senha-wrapper"> <input type="password" name="senha" id="senha"
                            placeholder="*******" readonly value="06022026">
                        <button type="button" onclick="showPass()" id="btnEye" class="eye-btn">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-input">
                <label for="permissao">Nível Permissão</label>
                <div class="input-wrapper">
                    <select name="permissao" id="permissao">
                        <option value="semValor" disabled selected>Selecione uma Opção</option>
                        <option value="NORMAL">Normal</option>
                        <option value="GESTOR">Gestor</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-confirmar-full confirmar">
                    Cadastrar <i class="bi bi-plus-lg"></i>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal: Edição de usuário -->
<div class="modal-fundo" id="edicaoUser" style="display: none">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Editar</h3>
            <button class="" id="" onclick="closeModal('edicaoUser')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="../actions/user/update_users.php" class="modal-form" method="POST">
            <div style="display: none;">
                <input type="number" id="id" name="id">
            </div>

            <div class="modal-input">
                <label for="nome">Nome:</label>
                <div class="input-wrapper">
                    <input type="text" name="nome" id="nome" placeholder="Ex: Matheus dos Ateus">
                </div>
            </div>

            <div class="modal-row">
                <div class="modal-input">
                    <label for="email">E-mail:</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" placeholder="exemplo@email.com">
                    </div>
                </div>

                <div class="modal-input">
                    <label for="senha">Senha:</label>
                    <div class="input-wrapper input-senha-wrapper"> <input type="password" name="senha" id="senha"
                            placeholder="*******" readonly value="06022026">
                        <button type="button" onclick="showPass()" id="btnEye" class="eye-btn">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-input">
                <label for="permissao">Nível Permissão</label>
                <select name="permissao" id="permissao">
                    <option value="semValor">Selecione uma Opção</option>
                    <option value="NORMAL">Normal</option>
                    <option value="GESTOR">Gestor</option>
                    <option value="ADMIN">Admin</option>
                </select>
            </div>
            <div class="modal-btn">
                <button type="submit" onclick="" class="btn-confirmar-full confirmar">Cadastrar <i
                        class="bi bi-plus-lg"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="modal-fundo" id="dell" style="display: none;">
    <div class="modal-box" style="width: 400px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3>Deletar</h3>
            <button onclick="closeModal('dell')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que quer deletar?</p>
        </div>
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <input type="number" name="id_usuario" id="id_usuario" style="display: none;">
            <button onclick="excluirUser(document.getElementById('id_usuario').value)"
                class="btn-confirmar-full confirmar">Sim</button>
            <button onclick="closeModal('dell')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Não</button>
        </div>
    </div>
</div>

<div class="modal-fundo" id="dellMachine" style="display: none;">
    <div class="modal-box" style="width: 400px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3>Deletar Máquina</h3>
            <button onclick="closeModal('dellMachine')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que quer deletar esta máquina?</p>
        </div>
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <input type="number" name="id_maquina_del" id="id_maquina_del" style="display: none;">
            <button onclick="excluirMaquina(document.getElementById('id_maquina_del').value)"
                class="btn-confirmar-full confirmar">Sim</button>
            <button onclick="closeModal('dellMachine')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Não</button>
        </div>
    </div>
</div>

<div class="modal-fundo" id="adicaoMachine" style="display: none">
    <div class="modal-box2">

        <div class="modal-header2">
            <h3>Registrar Máquina</h3>
            <button class="btn-close2" onclick="closeModal('adicaoMachine')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/machines/register_machines.php" class="modal-form2" method="POST">

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="denominacao">Denominação:</label>
                    <input type="text" name="denominacao" id="denominacao" placeholder="Ex: TORNO MECÂNICO">
                </div>
            </div>

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="marca">Marca:</label>
                    <input type="text" name="marca" id="marca" placeholder="ROMI">
                </div>
                <div class="modal-input-box2">
                    <label for="modelo">Modelo:</label>
                    <input type="text" name="modelo" id="modelo" placeholder="T 240">
                </div>
                <div class="modal-input-box2">
                    <label for="ano-fabricacao">Ano:
                        <input type="number" name="ano_fabricacao" id="ano-fabricacao" min="1800" max="2099" step="1"
                            placeholder="2020">
                    </label>
                </div>
            </div>

            <div class="modal-row2" id="tabela-frequencia">
                <div class="modal-input-box2">
                    <label for="ni">NI:</label>
                    <input type="text" name="numero_identificacao" id="numero_identificacao"
                        placeholder="1052694 SENAI">
                </div>
                <div class="modal-input-box2">
                    <label for="nserie">N° Série:</label>
                    <input type="text" name="numero_serie" id="numero_serie" placeholder="016-016057-452">
                </div>
                <div class="modal-input-box2">
                    <label for="setor">Setor:</label>
                    <input type="text" name="setor" id="setor" placeholder="CÉLULA 1">
                </div>
            </div>

            <div class="modal-row2">
                <div class="table-container2">
                    <table class="custom-table2">
                        <thead>
                            <tr>
                                <th style="width: 50px;">N°</th>
                                <th>Atividade ou item a verificar</th>
                                <th style="width: 150px;">Freq.</th>
                                <th style="width: 50px;"><i class="bi bi-trash-fill" id="lixeira"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <input type="text" name="atividade" id="atividade"
                                        placeholder="Ex: Inspecionar raspadores de cavacos">
                                </td>
                                <td>
                                    <select name="freq" id="freq">
                                        <option value="mensal">MENSAL</option>
                                        <option value="semestral">SEMESTRAL</option>
                                        <option value="trimestral">TRIMESTRAL</option>
                                        <option value="anual">ANUAL</option>
                                        <option value="bianual">BIANUAL</option>
                                    </select>
                                </td>
                                <td>
                                    <button type='button' class="btn-trash2"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-row2">
                <button type="button" class="btn-dashed2" id="addInspectionItemBtn">
                    <i class="bi bi-plus-circle-fill"></i> ADICIONAR NOVO ITEM DE INSPEÇÃO
                </button>
            </div>

            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Salvar Registro <i class="bi bi-check-lg"></i>
                </button>
            </div>

        </form>
    </div>
</div>

<div class="modal-fundo" id="edicaoMachine" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Editar Máquina</h3>
            <button class="btn-close2" onclick="closeModal('edicaoMachine')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="edit_denominacao">Denominação:</label>
                    <input type="text" name="denominacao" id="edit_denominacao" required>
                </div>
            </div>

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="edit_marca">Marca:</label>
                    <input type="text" name="marca" id="edit_marca">
                </div>
                <div class="modal-input-box2">
                    <label for="edit_modelo">Modelo:</label>
                    <input type="text" name="modelo" id="edit_modelo">
                </div>
            </div>

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="edit_numero_identificacao">NI:</label>
                    <input type="text" name="numero_identificacao" id="edit_numero_identificacao" required>
                </div>
                <div class="modal-input-box2">
                    <label for="edit_numero_serie">N° Série:</label>
                    <input type="text" name="numero_serie" id="edit_numero_serie">
                </div>
                <div class="modal-input-box2">
                    <label for="edit_ano_fabricacao">Ano:</label>
                    <input type="number" name="ano_fabricacao" id="edit_ano_fabricacao">
                </div>
                <div class="modal-input-box2">
                    <label for="edit_setor">Setor:</label>
                    <input type="text" name="setor" id="edit_setor">
                </div>
            </div>

            <div class="modal-row2">
                <div class="table-container2">
                    <table class="custom-table2" id="table-edit-inspection">
                        <thead>
                            <tr>
                                <th style="width: 50px;">N°</th>
                                <th>Atividade ou item a verificar</th>
                                <th style="width: 150px;">Freq.</th>
                                <th style="width: 50px;"><i class="bi bi-trash-fill"></i></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-row2">
                <button type="button" class="btn-dashed2" onclick="adicionarNovaLinhaEdit()">
                    <i class="bi bi-plus-circle-fill"></i> ADICIONAR NOVO ITEM DE INSPEÇÃO
                </button>
            </div>

            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Salvar Alterações <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal usuario senha 2 - Formulário para trocar senha -->
<div class="modal-fundo" id="changePassword" style="display: none;">
    <div class="modal-box2" style="width: 500px; max-width: 90%;">
        <div class="modal-header2">
            <h3>Trocar Senha</h3>
        </div>
        <div style="margin-bottom: 20px; color: var(--corTxt3); text-align: center;">
            <p>Por motivos de segurança, você deve alterar sua senha padrão.</p>
        </div>
        <form action="../actions/user/change_password.php" method="POST" class="modal-form2">
            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" name="nova_senha" id="nova_senha" required placeholder="Nova Senha">
                </div>
            </div>
            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" required
                        placeholder="Confirme a Senha">
                </div>
            </div>
            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Alterar Senha <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- modalResetPass2 removida: redefinição passa a executar diretamente a ação de reset -->

<div class="modal-fundo" id="modalAcessorios" style="display: none;">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>ACESSÓRIOS</h3> <!-- Título Vermelho via CSS -->
            <button class="btn-close2" onclick="closeModal('modalAcessorios')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form2">
            <div class="p-3 mb-3 bg-white border" style="border-radius: 15px;">
                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Equipamento
                    Selecionado:</small><br>
                <span class="h5 font-weight-bold text-dark" id="acessorio_maquina_nome">CARREGANDO...</span>
                <input type="hidden" id="acessorio_maquina_id">
            </div>

            <div class="modal-row2">
                <div class="table-container2">
                    <table class="custom-table2" id="table-acessorios">
                        <thead>
                            <tr>
                                <th>Denominação</th>
                                <th>Aplicação / Uso</th>
                                <th>Características</th>
                                <th style="width: 150px;">NI</th>
                                <th style="width: 50px;"><i class="bi bi-trash-fill"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Linhas inseridas via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-row2">
                <button type="button" class="btn-dashed2" onclick="adicionarAcessorio()">
                    <i class="bi bi-plus-circle-fill"></i> ADICIONAR NOVO ACESSÓRIO
                </button>
            </div>

            <div class="modal-footer2">
                <button type="button" class="btn-save2 confirmar" onclick="salvarAcessorios()">
                    Salvar Acessórios <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal-fundo" id="modalFAQ" style="display: none;">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Dúvidas Frequentes</h3>
            <button class="btn-close2" onclick="closeModal('modalFAQ')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-form2" style="max-height: 400px; overflow-y: auto;">
            <div class="faq-item mb-3">
                <h5 class="font-weight-bold text-dark"><i class="bi bi-question-circle-fill text-primary"></i> Como
                    cadastrar uma nova máquina?</h5>
                <p class="text-muted ml-4">Vá para a página de Máquinas e clique no botão "+ Adicionar Máquina".
                    Preencha os dados solicitados e salve.</p>
            </div>

            <div class="faq-item mb-3">
                <h5 class="font-weight-bold text-dark"><i class="bi bi-question-circle-fill text-primary"></i> Como
                    resetar a senha de um usuário?</h5>
                <p class="text-muted ml-4">Na página de Usuários, clique no ícone de chave amarela ao lado do nome do
                    usuário. A senha será resetada para o padrão.</p>
            </div>

            <div class="faq-item mb-3">
                <h5 class="font-weight-bold text-dark"><i class="bi bi-question-circle-fill text-primary"></i> Como
                    adicionar acessórios a uma máquina?</h5>
                <p class="text-muted ml-4">Na página de Máquinas, clique no ícone roxo de ferramentas na linha da
                    máquina desejada. Uma janela se abrirá para gerenciar os acessórios.</p>
            </div>

            <div class="faq-item mb-3">
                <h5 class="font-weight-bold text-dark"><i class="bi bi-question-circle-fill text-primary"></i> O que
                    fazer se esquecer minha senha?</h5>
                <p class="text-muted ml-4">Entre em contato com um administrador do sistema para que ele realize o reset
                    da sua senha.</p>
            </div>
        </div>

        <div class="modal-footer2">
            <button type="button" class="btn-save2 confirmar" onclick="closeModal('modalFAQ')">
                Entendi <i class="bi bi-check-lg"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal Ações Corretiva -->
<div class="modal-fundo" id="corretiva" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Corretiva Máquina</h3>
            <button class="btn-close2" onclick="closeModal('corretiva')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="data_corretiva">Date:</label>
                    <input type="date" name="data_corretiva" id="data_corretiva">
                </div>
                <div class="modal-input-box2">
                    <label for="edit_modelo">Responsavel:</label>
                    <input type="text" name="modelo" id="edit_modelo" placeholder="Ex: Josias da Silva">
                </div>
            </div>

            <div class="modal-row2">
                <div class="modal-input-box2">
                    <label for="edit_marca">Descrição:</label>
                    <textarea type="text" name="marca" id="edit_marca" placeholder="Descrição da corretiva de Máquina">
                    </textarea>
                </div>
            </div>

            <div class="modal-row2">

            </div>

            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Salvar <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>



<!-- Modal para Ações Preventivas  -->
<div class="modal-fundo" id="preventiva" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Preventiva Máquina</h3>
            <button class="btn-close2" onclick="closeModal('preventiva')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-row2">
                <div class="table-container2">
                    <table class="custom-table2" id="table-edit-inspection">
                        <thead>
                            <tr>
                                <th style="width: 50px;">N°</th>
                                <th>Atividade ou item a verificar</th>
                                <th style="width: 150px;">Freq.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-row2">

            </div>

            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Salvar <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>



<!-- Modal para Mostrar as informações da Maquina -->
<div class="modal-fundo" id="preventiva" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Informação Maquina</h3>
            <button class="btn-close2" onclick="closeModal('preventiva')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-row2">
                <div class="table-container2">
                    <table class="custom-table2" id="table-edit-inspection">
                        <thead>
                            <tr>
                                <th style="width: 50px;">N°</th>
                                <th>Atividade ou item a verificar</th>
                                <th style="width: 150px;">Freq.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-row2">

            </div>

            <div class="modal-footer2">
                <button type="submit" class="btn-save2 confirmar">
                    Salvar <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Visualizar -->
<div class="modal-fundo" id="view" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Nome da Máquina</h3>
            <button type="button" class="btn-close2" onclick="closeModal('view')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-body-layout">
                <div class="info-section">

                    <div class="info-item">
                        <label>Marca</label>
                        <span class="info-value">Toyota</span>
                    </div>

                    <div class="info-item">
                        <label>Modelo</label>
                        <span class="info-value">fb8-560</span>
                    </div>

                    <div class="info-item">
                        <label>N° Identificação</label>
                        <span class="info-value">.546546116454</span>
                    </div>

                    <div class="info-item">
                        <label>N° Série</label>
                        <span class="info-value">698879</span>
                    </div>

                </div>

                <div class="image-section">
                    <div class="img-placeholder">
                        <!-- <i class="bi bi-camera-fill"></i> -->
                        <img src="https://picsum.photos/200/300" alt="Imagem Aleatória">

                    </div>
                </div>
            </div>

            <div class="modal-footer2">

            </div>
        </form>
    </div>
</div>

<!-- Anexos Img
<div class="modal-fundo" id="anexos" style="display: none">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Anexos</h3>
            <button type="button" class="btn-close2" onclick="closeModal('anexos')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="../actions/machines/update_machine.php" class="modal-form2" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-body-layout">
                <div class="info-section">
                    
                    <div class="info-item">
                        <p>Anexos</p>
                    </div>

                </div>

                <div class="image-section">
                    <div class="img-placeholder" style="background:transparent; align-items:flex-start;">
                        <button type="button" class="btn confirmar">Adicionar Anexo</button>

                        </div>
                </div>
            </div>
            
            <div class="modal-footer2">
                
                </div>
        </form>
    </div>
</div> -->

<!-- Modal CUSTOMIZADA DE PREVENTIVA (Extraída e Adaptada) -->
<div class="modal-fundo" id="modalPreventiva" style="display: none;">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Checklist de Manutenção</h3>
            <button class="btn-close2" onclick="closeModal('modalPreventiva')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="checklist-info-box">
            <div class="checklist-actions-row">
                <div class="checklist-col-equipment">
                    <label class="section-label">Equipamento:</label>
                    <div class="h5 font-weight-bold mb-0" id="nome_maquina_checklist" style="font-size: 1.25rem;">
                        Carregando...</div>
                    <input type="hidden" id="id_maquina_checklist">
                </div>
                <div class="checklist-col-filter">
                    <label class="section-label text-primary" style="color: #007bff;">Ciclo de Manutenção:</label>
                    <select class="form-control filter-select" id="selectCiclo" onchange="aplicarFiltrosChecklist()">
                        <option value="TODOS">TODOS OS ITENS</option>
                        <option value="mensal">MENSAL</option>
                        <option value="trimestral">TRIMESTRAL</option>
                        <option value="semestral">SEMESTRAL</option>
                        <option value="anual">ANUAL</option>
                    </select>
                </div>
                <div class="checklist-col-status-group">
                    <div class="status-select-container">
                        <label class="section-label text-danger" style="color: #dc3545;">Filtrar por Status:</label>
                        <select class="form-control filter-select" id="selectStatus"
                            onchange="aplicarFiltrosChecklist()">
                            <option value="TODOS">MOSTRAR TODOS</option>
                            <option value="VENCIDOS">ITENS VENCIDOS</option>
                            <option value="PROXIMOS">ITENS PRÓXIMOS</option>
                        </select>
                    </div>
                    <button class="btn-clear-inline" onclick="limparFiltros()">Limpar</button>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
            <table class="checklist-table" id="tabelaChecklist">
                <thead>
                    <tr>
                        <th style="width: 40px;">Nº</th>
                        <th>Atividade ou item a verificar</th>
                        <th style="width: 110px;">Freq.</th>
                        <th style="width: 120px;">Última Data</th>
                        <th style="width: 120px;">Próxima Data</th>
                        <th style="width: 60px;">R</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Itens serão carregados via AJAX aqui -->
                </tbody>
            </table>
        </div>

        <div class="modal-footer2 p-0" style="margin-top: 20px;">
            <div class="modal-input" style="margin-bottom: 10px;">
                <label for="obs_preventiva">Observações Gerais:</label>
                <textarea id="obs_preventiva" class="form-control"
                    style="width: 100%; border: 1px solid #ddd; border-radius: 10px; padding: 10px;" rows="2"
                    placeholder="Alguma observação sobre essa manutenção?"></textarea>
            </div>
            <button class="btn-save-preventiva" onclick="finalizarPreventiva()">
                Finalizar Inspeção Selecionada <i class="bi bi-check-circle-fill ml-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal Histórico de Manutenção -->
<div class="modal-fundo" id="modalHistorico" style="display: none;">
    <div class="modal-box2">
        <div class="modal-header2">
            <h3>Histórico de Manutenção</h3>
            <button class="btn-close2" onclick="closeModal('modalHistorico')"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="checklist-info-box">
            <h5 id="hist_maquina_nome" class="text-center font-weight-bold" style="color: #333;">Carregando...</h5>
        </div>

        <div class="table-container2" style="max-height: 50vh; overflow-y: auto;">
            <table class="custom-table2" id="tabelaHistorico">
                <thead>
                    <tr>
                        <th>Item Verificado</th>
                        <th>Quem Verificou</th>
                        <th style="width: 150px;">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Linhas inseridas via JS -->
                </tbody>
            </table>
        </div>

        <div class="modal-footer2">
            <button type="button" class="btn-save2" style="background-color: #6c757d;"
                onclick="closeModal('modalHistorico')">
                Fechar
            </button>
        </div>
    </div>
</div>

<!-- Modal usuario senha 1 - Confirmação de Reset -->
<div class="modal-fundo" id="resetPass" style="display: none;">
    <div class="modal-box" style="width: 450px; padding: 20px;">
        <div class="modal-header" style="margin-bottom: 20px;">
            <h3>Resetar Senha</h3>
            <button onclick="closeModal('resetPass')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; margin-bottom: 25px; color: var(--corTxt3);">
            <p>Tem certeza que quer resetar a senha deste usuário para 'senaisp'?</p>
        </div>
        <div style="width: 100%; display: flex; gap: 10px; justify-content: center;">
            <input type="number" name="id_usuario_reset_confirm" id="id_usuario_reset_confirm" style="display: none;">
            <button onclick="resetarSenha(document.getElementById('id_usuario_reset_confirm').value)"
                class="btn-confirmar-full confirmar">Sim</button>
            <button onclick="closeModal('resetPass')" type="button" class="btn-confirmar-full confirmar"
                style="background-color: var(--corBase);">Não</button>
        </div>
    </div>
</div>