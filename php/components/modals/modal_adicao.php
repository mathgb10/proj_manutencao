<div class="modal-fundo">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Registrar</h3>
            <button class="" onclick="closeModal('adicaoUser')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="../actions/register_user.php" class="modal-form" method="POST">
            <div class="modal-input">
                <input type="text" name="nome" id="nome" placeholder="Nome:">
            </div>
            <div class="modal-input">
                <input type="email" name="email" id="email" placeholder="E-mail:">
            </div>
            <div class="modal-input">
                <input type="password" name="senha" id="senha" placeholder="Senha:">
            </div>
            <div class="modal-input">
                <select name="permissao" id="permissao">
                    <option value="semValor" disabled selected>Selecione uma Opção</option>
                    <option value="NORMAL">Normal</option>
                    <option value="GESTOR">Gestor</option>
                    <option value="ADMIN">Admin</option>
                </select>
            </div>
            <div class="modal-btn">
                <button type="submit" onclick="">Cadastrar <i class="bi bi-plus-lg"></i></button>
            </wdiv>
        </form>

    </div>
</div>

