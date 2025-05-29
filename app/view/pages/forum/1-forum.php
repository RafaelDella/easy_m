<?php
// Este arquivo é a página do formulário de Perfil Financeiro.
// Ele inclui a sidebar e o header, e contém a lógica do formulário.

// 1. Inicie a sessão se você estiver usando sessões para autenticação ou dados do usuário.
session_start();

// 2. Lógica de verificação de autenticação.
// Se o usuário não estiver autenticado, redirecione para a página de login.
// O caminho deve ser ajustado conforme a localização do seu arquivo de login
// em relação a este arquivo (perfil_financeiro.php).
// Exemplo: se perfil_financeiro.php está em 'easy_m1/app/view/pages/forms_perfil/'
// e o login está em 'easy_m1/forms_login/1-forms_login.html', o caminho seria '../../../../forms_login/1-forms_login.html'.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html"); // Ajuste este caminho se necessário!
    exit;
}

// 3. Inclua a conexão com o banco de dados.
// O caminho abaixo assume que este arquivo está em 'easy_m1/app/view/pages/forms_perfil/'
// e 'db.php' está na raiz do projeto 'easy_m1/'.
require_once __DIR__ . '../../../../db.php';

// Crie uma instância da classe DB e conecte-se ao banco de dados.
$db = new DB();
$pdo = $db->connect();

// Obtenha o ID do usuário da sessão.
$id_usuario = $_SESSION['id_usuario'];

// 4. Obtenha os dados do usuário (necessários para a sidebar e header).
// Busca o nome e perfil do usuário da tabela 'Usuario'.
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

// Defina as variáveis $nome e $perfilUsuario com os dados do banco de dados,
// ou use valores de fallback se os dados não forem encontrados.
$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Nenhuma lógica de dados específica do dashboard é necessária aqui,
// pois esta é a página do formulário de perfil.
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Financeiro - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/pages/7-forum.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

</head>
    <body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>
        <main>
            <h1>Fórum de Dicas</h1>

            <div id="forum"></div>

            <div class="modal" id="modal">
                <div class="modal-content">
                    <h3 id="modal-title"></h3>
                    <p id="modal-desc"></p>
                    <p id="modal-content"></p>
                    <button class="close-btn" onclick="closeModal()">Fechar</button>
                </div>
            </div>
        </main>
        <script src="../../../assets/js/components/sidebar.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
        <script>
        const perfilUsuario = <?= json_encode($perfilUsuario) ?>;

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function openModal(titulo, descricao, conteudo) {
            document.getElementById('modal-title').innerText = titulo;
            document.getElementById('modal-desc').innerText = descricao;
            document.getElementById('modal-content').innerText = conteudo;
            document.getElementById('modal').style.display = 'flex';
        }

        fetch('2-dicas.json')
            .then(response => response.json())
            .then(data => {
                const forum = document.getElementById('forum');
                const dicasPerfil = data.filter(dica => dica.tipo === perfilUsuario);

                if (dicasPerfil.length > 0) {
                    const container = document.createElement('div');
                    container.classList.add('carousel-container');

                    const title = document.createElement('h2');
                    title.textContent = 'Perfil: ' + perfilUsuario;
                    container.appendChild(title);

                    const wrapper = document.createElement('div');
                    wrapper.classList.add('carousel');

                    dicasPerfil.forEach(dica => {
                        const card = document.createElement('div');
                        card.classList.add('card');

                        const h3 = document.createElement('h3');
                        h3.textContent = dica.titulo;
                        card.appendChild(h3);

                        const p = document.createElement('p');
                        p.textContent = dica.descricao.substring(0, 100) + '...';
                        card.appendChild(p);

                        const autor = document.createElement('div');
                        autor.classList.add('autor');
                        autor.textContent = 'Autor: ' + dica.autor;
                        card.appendChild(autor);

                        const btn = document.createElement('button');
                        btn.textContent = 'Ver mais';
                        btn.onclick = () => openModal(dica.titulo, dica.descricao, dica.conteudo);
                        card.appendChild(btn);

                        wrapper.appendChild(card);
                    });

                    container.appendChild(wrapper);
                    forum.appendChild(container);

                    $(wrapper).slick({
                        infinite: true,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 8000,
                        arrows: true,
                        dots: true,
                        responsive: [
                            { breakpoint: 1024, settings: { slidesToShow: 3 }},
                            { breakpoint: 768, settings: { slidesToShow: 2 }},
                            { breakpoint: 480, settings: { slidesToShow: 1 }}
                        ]
                    });
                } else {
                    forum.innerHTML = "<p>Nenhuma dica disponível para seu perfil.</p>";
                }
            })
            .catch(error => {
                console.error("Erro ao carregar dicas:", error);
                document.getElementById('forum').innerHTML = "<p>Erro ao carregar dicas.</p>";
            });
    </script>
    </body>

</html>