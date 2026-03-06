# 🛠️ Sistema de Manutenção - proj_manutencao

![Dashboard Mockup](assets/dashboard_mockup.png)

> [!IMPORTANT]
> **proj_manutencao** é um ecossistema de gerenciamento industrial desenvolvido para otimizar o fluxo de manutenções no ambiente SENAI. O sistema prioriza performance, segurança (ACL) e uma interface moderna baseada em variáveis OKLCH e design responsivo.

---

## 🚀 Tecnologias & Stack

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)

- **Backend**: PHP (Arquitetura RESTful, sem PDO, utilizando `mysqli`).
- **Frontend**: Vanilla JavaScript (Async Fetch API) e CSS puro com design tokens modernos.
- **Banco de Dados**: MySQL com estrutura normalizada e logs de auditoria.

---

## ✨ Funcionalidades Principais

- 🔐 **Sistema de Controle de Acesso (ACL)**: Bloqueio de rotas baseado em perfis (Admin/Comum).
- 📊 **Dashboard Dinâmico**: Cards informativos com status em tempo real e gráficos de manutenção.
- 📋 **Gestão de Máquinas**: CRUD completo para equipamentos.
- 🔔 **Notificações Inteligentes**: Alertas visuais e sistema de logs de eventos.
- 📂 **Upload de Documentos**: Interface DropZone para anexos técnicos de manutenção.
- 👁️ **Visualização Segura**: Toggle de visibilidade de senhas e validações rigorosas de login.

---

## 🏗️ Arquitetura do Projeto

O projeto segue um padrão de organização modular para facilitar a escalabilidade:

```text
/
├── php/
│   ├── actions/      # Lógica de processamento (POST/GET)
│   ├── components/   # Headers, Footers e Navbars reutilizáveis
│   ├── controllers/  # Orquestração de dados
│   └── views/        # Interfaces do usuário
├── sql/              # Scripts de migração e estrutura do banco
├── js/               # Scripts globais e utilitários assíncronos
└── css/              # Design System (Variáveis OKLCH e Temas)
```

---

> [!TIP]
> O sistema utiliza animações premium e redimensionamento dinâmico para garantir a melhor experiência em qualquer dispositivo.
