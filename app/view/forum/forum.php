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
            <button class="close-btn" onclick="closeModal()">Fechar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function openModal(titulo, descricao) {
            document.getElementById('modal-title').innerText = titulo;
            document.getElementById('modal-desc').innerText = descricao;
            document.getElementById('modal').style.display = 'flex';
        }

        fetch('Dicas.json')
            .then(response => response.json())
            .then(data => {
                const perfis = ['Endividado', 'Poupador', 'Doméstico'];
                const forum = document.getElementById('forum');

                perfis.forEach(perfil => {
                    const dicasPerfil = data.filter(dica => dica.tipo === perfil);

                    if (dicasPerfil.length > 0) {
                        const container = document.createElement('div');
                        container.classList.add('carousel-container');

                        const title = document.createElement('h2');
                        title.textContent = 'Perfil: ' + perfil;
                        container.appendChild(title);

                        const wrapper = document.createElement('div');
                        wrapper.classList.add('carousel');

                        dicasPerfil.forEach(dica => {
                            const card = document.createElement('div');
                            card.classList.add('card');

                            if (dica.imagem_url) {
                                const img = document.createElement('img');
                                img.src = dica.imagem_url;
                                card.appendChild(img);
                            }

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
                            btn.onclick = () => openModal(dica.titulo, dica.descricao);
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
                                {
                                    breakpoint: 1024,
                                    settings: {
                                        slidesToShow: 3
                                    }
                                },
                                {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: 2
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        slidesToShow: 1
                                    }
                                }
                            ]
                        });
                    }
                });
            });
    </script>
</body>
</html>
