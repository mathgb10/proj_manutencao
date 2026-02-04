// Carrega o Ultimo tema salvo no LocalStorage e define uma saudação de acordo com a hora do dia.
window.onload = () => {
    // Se tiver tema no LocalStorage eu pego ele se não coloca "claro" mesmo
    let tema = localStorage.getItem('tema') || 'claro';

    // document.getElementById("img-Senai").innerHTML = '<img src="assets/imgs/senailogo.png" alt="Logo Senai" id="imagem-senai">';

    // Definindo atributo "data-tema" com valor da minha variavel tema
    document.documentElement.setAttribute("data-tema", tema);

    // Isso é para mudar o icone dos btns, mas tive que colocar uma condicional para verificar se eles existem ou não
    let btnTema = document.getElementById("tema");
    let imgSenai = document.getElementById("senai-logo");
    let imgSenai2 = document.getElementById("senai-logo2");
    if (tema == 'escuro') {
        if (imgSenai != undefined) {
            imgSenai.src = 'assets/imgs/senaiEscuro.png';
        }
        if (imgSenai2 != undefined) {
            imgSenai2.src = '../../assets/imgs/senaiEscuro.png';
        }
        if (btnTema != undefined) {
            document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
    } else {
        if (imgSenai != undefined) {
            imgSenai.src = 'assets/imgs/senailogo.png';

        }
        if (imgSenai2 != undefined) {
            imgSenai2.src = '../../assets/imgs/senailogo.png';
        }
        if (btnTema != undefined) {
            document.getElementById("tema").innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        }
    }

    let local_txt = document.getElementById("msg_especial");

    if (local_txt != undefined) {
        let data = new Date();
        let hora = data.getHours();
        // console.log(hora);

        if (hora >= 6 && hora < 12) {
            local_txt.innerHTML = "<strong style='color: var(--corBase)'>Bom Dia!</strong>";
        } else if (hora >= 12 && hora < 18) {
            local_txt.innerHTML = "<strong style='color: var(--corBase)'>Boa Tarde!</strong>";
        } else if (hora >= 18) {
            local_txt.innerHTML = "<strong style='color: var(--corBase)'>Boa Noite!</strong>";
        } else {
            local_txt.innerHTML = "<strong style='color: var(--corBase)'>Olá!</strong>";
        }
    }
}

// Muda a visibilidade do campo "senha" 
function showPass() {
    // Coleto e armazenos o Btn do Olho e o Input de Senha
    let eye = document.getElementById("btnEye");
    let inputPass = document.getElementById("senha");

    // Se o conteudo for "Olho Aberto" troque o tipo do Input para text e o conteudo do Btn para "Olho Fechado"
    if (eye.innerHTML.match('<i class="bi bi-eye-fill"></i>')) {
        eye.innerHTML = '<i class="bi bi-eye-slash"></i>';
        inputPass.type = "text";
        // Caso não for "Olho Aberto" toque o tipo do Input para password e o conteudo do Btn para "Olho Aberto"
    } else {
        eye.innerHTML = '<i class="bi bi-eye-fill"></i>';
        inputPass.type = "password";
    }
    console.log(eye.innerHTML);
}

// Muda o Tema atual e armazena no LocalStorage
// A rapaziada com todo respeito n vou comentar if nisso aq n, acho q vcs ja entenderam R: Tendi nada não kkkkk

function changeTheme() {
    let tema_atual = document.documentElement.getAttribute("data-tema")
    let imgSenai2 = document.getElementById("senai-logo2");

    if (tema_atual == "escuro") {
        localStorage.removeItem('tema');
        document.documentElement.setAttribute("data-tema", "claro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        localStorage.setItem('tema', 'claro');
        imgSenai2.src = '../../assets/imgs/senailogo';
        if (imgSenai2 != undefined) {
            imgSenai2.src = '../../assets/imgs/senailogo.png';
        }
    } else {
        localStorage.removeItem('tema');
        document.documentElement.setAttribute("data-tema", "escuro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('tema', 'escuro');
        if (imgSenai2 != undefined) {
            imgSenai2.src = '../../assets/imgs/senaiEscuro.png';
        }
    }
}

function changeSairBtn(oc) {
    let btnSair = document.querySelector(".sair");
    if (oc == "open") {
        btnSair.innerHTML = 'Sair <i class="bi bi-door-open-fill"></i>';
    } else {
        btnSair.innerHTML = 'Sair <i class="bi bi-door-closed-fill"></i>';
    }
}

function closeModal(qual) {
    if (qual == 'acesso') {
        document.getElementById('acesso').style.display = 'none';
        // Seria Legal tirar o ?acesso=negado dps que fechar
    } else if (qual == 'adicaoUser') {
        document.getElementById('adicaoUser').style.display = 'none';
    } else if (qual == 'edicaoUser') {
        document.getElementById('edicaoUser').style.display = 'none';
    }
}

// botoes de tema | notificação e suas respectivas animações ou funcionalidades

const notificacao = document.getElementById("notificacao");
const fechar = document.getElementById("fechar-modal");
let arrow = document.getElementById("fechar-nav");

if (notificacao != undefined) {

    notificacao.addEventListener('mouseover', () => {
        notificacao.style.animation = 'animTremendo 0.25s linear';
    })

    notificacao.addEventListener('mouseout', () => {
        notificacao.style.animation = 'none';
    })

    notificacao.addEventListener('click', () => {
        let fundo = document.querySelector(".modal-fundo").style.display = 'flex';
        let modal = document.querySelector(".modal-notificacao").style.display = 'flex';
    })

    fechar.addEventListener('click', () => {
        let fundo = document.querySelector(".modal-fundo").style.display = 'none';
        let modal = document.querySelector(".modal-notificacao").style.display = 'none';
    })
}

// funcao fechar e abrir navbar
if (arrow != undefined) {
    arrow.addEventListener('mouseenter', () => {
        arrow.style.animation = 'arrow 0.8s 2 linear';
    });

    arrow.addEventListener('mouseleave', () => {
        arrow.style.animation = 'none';
    });

    arrow.addEventListener('click', () => {
        let sidebar = document.querySelector(".sidebar");
        let main = document.querySelector(".sec-main")
        let navLinks = document.querySelector(".div-links");
        let divImg = document.querySelector(".div-img");
        let divConfig = document.querySelector(".div-configs");

        sidebar.style.animation = 'navbarAnim 0.25s linear';
        arrow.style.animation = 'arrowSwapanim 0.25s linear';
        navLinks.style.display = 'none';
        divImg.style.display = 'none';
        divConfig.style.display = 'none';

        setInterval(() => {
            sidebar.style.width = '0px';
            main.style = 'padding-left: 10px';
            arrow.innerHTML = '<i class="bi bi-arrow-right-circle-fill"></i>';
            arrow.style.animation = 'none';
        }, 250)

    });
}


function showModal(qual, id) {
    if (qual == "adicaoUser") {
        document.getElementById(qual).style.display = "flex";
    } else if (qual == "edicaoUser") {
        document.getElementById("id").value = id;
        document.getElementById(qual).style.display = "flex";
    } else if (qual == "editarMaquina") {
        document.getElementById("id_maquina").value = id;
        document.getElementById(qual).style.display = "flex";
    }
}

function excluirUser(id) {
    if (confirm("Tem certeza que deseja excluir este usuário?")) {
        fetch('../actions/user/delete_users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Usuário excluído com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao excluir usuário: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a solicitação.');
            });
    }
}
function excluirMaquina(id) {
    if (confirm("Tem certeza que deseja excluir essa máquina?")) {
        fetch('../actions/machines/delete_machines.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede ou arquivo não encontrado');
                }
                return response.json();
            })
            .then(data => {
                console.log(data);


                if (data.success === true) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a solicitação. Verifique o console (F12).');
            });
    }
}