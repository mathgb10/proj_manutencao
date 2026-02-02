<div class="modal-fundo" id="adicaoUser">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Registrar</h3>
                <button class="" onclick="closeModal('adicaoUser')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="../actions/user/register_user.php" class="modal-form" method="POST">
                <div class="modal-row">
                    <div class="modal-input">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" placeholder="Exemplo da Silva">
                    </div>
                    <div class="modal-input">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="exemplo@email.com">
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-input">
                        <label for="senha">Senha:</label>
                        <div class="input-senha">
                            <input type="password" name="senha" id="senha" placeholder="*****">
                            <button type="button" onclick="showPass()" id="btnEye"><i class="bi bi-eye-fill"></i></button>
                        </div>
                    </div>
                    <div class="modal-input">
                        <label for="permissao">Nível Permissão</label>
                        <select name="permissao" id="permissao">
                            <option value="semValor" disabled selected>Selecione uma Opção</option>
                            <option value="NORMAL">Normal</option>
                            <option value="GESTOR">Gestor</option>
                            <option value="ADMIN">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-btn">
                    <button type="submit" onclick="" class="btn confirmar">Cadastrar <i class="bi bi-plus-lg"></i></button>
                </div>
            </form>

        </div>
    </div>