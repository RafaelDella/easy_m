<?php
    session_start();
    require_once '../../db.php';

    $db = new DB();
    $conn = $db->connect();

    $userId = $_SESSION['user_id'] ?? null;
    $perfil = "";

    // Buscar perfil com base no id de usuário
    if ($userId) {
        $userRole = $conn->prepare("SELECT perfil FROM usuario WHERE id = :id");
        $userRole->execute([':id' => $userId]);
        $perfilBanco = $userRole->fetchColumn();

        if ($perfilBanco) {
            $perfil = $perfilBanco;
        } else {
            error_log("Perfil não encontrado com ID: $userId");
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Fórum EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="../../assets/forum/forum.css">
</head>

<body>
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

    <script>
        const perfilUsuario = <?= json_encode($perfil) ?>;

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function openModal(titulo, descricao, conteudo) {
            document.getElementById('modal-title').innerText = titulo;
            document.getElementById('modal-desc').innerText = descricao;
            document.getElementById('modal-content').innerText = conteudo;
            document.getElementById('modal').style.display = 'flex';
        }

        fetch('Dicas.json')
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
                        autoplaySpeed: 4000,
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</body>
</html>
