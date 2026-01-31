// Carrega o Ultimo tema salvo no LocalStorage
window.onload = () => {
    // Se tiver tema no LocalStorage eu pego ele se não coloca "claro" mesmo
    let tema = localStorage.getItem('tema') || 'claro';

    // Definindo atributo "data-tema" com valor da minha variavel tema
    document.documentElement.setAttribute("data-tema", tema);

    // Isso é para mudar o icone dos btns, mas tive que colocar uma condicional para verificar se eles existem ou não
    var btnTema = document.getElementById("tema");
    if (tema == 'escuro') {
        if(btnTema != undefined){
            document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
    } else {
        if(btnTema != undefined){
            document.getElementById("tema").innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        }
    }
}

// Muda a visibilidade do campo "senha" 
function showPass() {
    // Coleto e armazenos o Btn do Olho e o Input de Senha
    var eye = document.getElementById("btnEye");
    var inputPass = document.getElementById("senha");

    // Se o conteudo for "Olho Aberto" troque o tipo do Input para text e o conteudo do Btn para "Olho Fechado"
    if(eye.innerHTML.match('<i class="bi bi-eye-fill"></i>')){
        eye.innerHTML = '<i class="bi bi-eye-slash"></i>';
        inputPass.type = "text";
    // Caso não for "Olho Aberto" toque o tipo do Input para password e o conteudo do Btn para "Olho Aberto"
    } else {
        eye.innerHTML = '<i class="bi bi-eye-fill"></i>';
        inputPass.type = "password";
    }

}

// Muda o Tema atual e armazena no LocalStorage
// A rapaziada com todo respeito n vou comentar if nisso aq n, acho q vcs ja entenderam 
function changeTheme() {
    var tema_atual = document.documentElement.getAttribute("data-tema") 

    if(tema_atual == "escuro"){
        localStorage.removeItem('tema')
        document.documentElement.setAttribute("data-tema","claro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        localStorage.setItem('tema','claro')
    } else{
        localStorage.removeItem('tema')
        document.documentElement.setAttribute("data-tema","escuro");
        document.getElementById("tema").innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('tema','escuro')
    }
}