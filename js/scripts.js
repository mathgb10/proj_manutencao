// Carrega o Ultimo tema salvo no LocalStorage e define uma saudação de acordo com a hora do dia.
window.onload = () => {
    // Se tiver tema no LocalStorage eu pego ele se não coloca "claro" mesmo
    let tema = localStorage.getItem('tema') || 'claro';

    // Definindo atributo "data-tema" com valor da minha variavel tema
    document.documentElement.setAttribute("data-tema", tema);

    // Isso é para mudar o icone dos btns, mas tive que colocar uma condicional para verificar se eles existem ou não
    let btnTema = document.getElementById("tema");
    if (tema == 'escuro') {
        if (btnTema != undefined) {
            document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
    } else {
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

    if (tema_atual == "escuro") {
        localStorage.removeItem('tema')
        document.documentElement.setAttribute("data-tema", "claro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        localStorage.setItem('tema', 'claro')
    } else {
        localStorage.removeItem('tema')
        document.documentElement.setAttribute("data-tema", "escuro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('tema', 'escuro')
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
    }
}

// botoes de tema | notificação e suas respectivas animações ou funcionalidades

const notificacao = document.getElementById("notificacao");
const fechar = document.getElementById("fechar-modal");

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


function showModal(qual, id){
    return 
    // Colocar a exibição de Modal aqui
}