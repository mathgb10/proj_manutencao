<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal de Manutenção</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Reset básico e Fontes */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* 1. Fundo do Modal */
        .modal-fundo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fundo escuro transparente */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* 2. Caixa do Modal */
        .modal-box2 {
            background-color: #f5f5f5;
            width: 90%;
            max-width: 700px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        /* 3. Cabeçalho */
        .modal-header2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .modal-header2 h2 {
            color: #ff6b6b;
            /* Cor avermelhada do título */
            font-size: 1.5rem;
            font-weight: 700;
        }

        .btn-close2 {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }

        /* 4. Formulário e Inputs */
        .modal-form2 {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-input-box2 {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .modal-input-box2 label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        /* Estilo genérico para inputs e selects */
        .modal-form2 input,
        .modal-form2 select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            background-color: #fff;
            outline: none;
        }

        /* Linha para Data e Responsável */
        .modal-row2 {
            display: flex;
            gap: 20px;
        }

        /* 5. Tabela Customizada */
        .table-container2 {
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
            /* Para arredondar cantos da tabela */
        }

        .custom-table2 {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table2 thead {
            background-color: #ff5252;
            /* Fundo vermelho do header da tabela */
            color: white;
        }

        .custom-table2 th {
            padding: 12px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .custom-table2 tbody tr {
            background-color: #e6e6e6;
            /* Fundo cinza da linha */
            border-bottom: 10px solid #f5f5f5;
            /* Espaçamento fake entre linhas */
        }

        .custom-table2 td {
            padding: 15px;
            vertical-align: middle;
            color: #333;
            font-weight: 600;
        }

        /* Inputs dentro da tabela */
        .custom-table2 input[type="text"] {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.05);
            border: none;
            color: #666;
        }

        .custom-table2 select {
            background-color: #dcdcdc;
            border: none;
            padding: 5px 10px;
        }

        /* Ícone de lixeira */
        .bi-trash-fill {
            cursor: pointer;
            font-size: 1.2rem;
            color: #000;
        }

        /* Botão tracejado (pedido na lista de classes) */
        .btn-dashed2 {
            margin-top: 10px;
            width: 100%;
            border: 2px dashed #999;
            background-color: transparent;
            padding: 10px;
            border-radius: 8px;
            color: #666;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-dashed2:hover {
            background-color: #e0e0e0;
        }

        /* 6. Rodapé e Botão Salvar */
        .modal-footer2 {
            margin-top: 20px;
        }

        .btn-save2 {
            width: 100%;
            padding: 15px;
            background-color: #00cba9;
            /* Verde Água / Teal */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: background 0.3s;
        }

        .btn-save2:hover {
            background-color: #00b395;
        }
    </style>
</head>

<body>

    <div class="modal-fundo">
        <div class="modal-box2">

            <div class="modal-header2">
                <h2>Registrar Preventiva</h2>
                <button class="btn-close2">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="modal-form2">

                <div class="modal-input-box2">
                    <select>
                        <option>Torno Mecânico - Romi T 240</option>
                        <option>Fresadora Universal</option>
                    </select>
                </div>

                <div class="modal-row2">
                    <div class="modal-input-box2">
                        <label>Data:</label>
                        <input type="date" value="2026-02-09">
                    </div>
                    <div class="modal-input-box2">
                        <label>Visto/Responsável:</label>
                        <input type="text" placeholder="Nome do Responsável">
                    </div>
                </div>

                <div class="table-container2">
                    <table class="custom-table2">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Nº</th>
                                <th>Atividade de Manutenção</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <input type="text" value="Inspecionar raspadores de cavacos" readonly>
                                </td>
                                <td>
                                    <select>
                                        <option>Feito</option>
                                        <option>Pendente</option>
                                    </select>
                                </td>
                                <td>
                                    <i class="bi bi-trash-fill"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button class="btn-dashed2">
                    <i class="bi bi-plus-circle-fill"></i> Adicionar Atividade
                </button>

            </div>

            <div class="modal-footer2">
                <button class="btn-save2 confirmar">
                    Salvar Registro
                    <i class="bi bi-check-lg" style="display:none"></i>
                </button>
            </div>

        </div>
    </div>

</body>

</html>