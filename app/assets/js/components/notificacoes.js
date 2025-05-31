document.addEventListener('DOMContentLoaded', () => {
    const sino = document.getElementById('iconeNotificacao');
    const painel = document.getElementById('painelNotificacoes');
    const lista = document.getElementById('listaNotificacoes');
    const contador = document.getElementById('contador-alertas');

    // Alternar visibilidade do painel
    sino.addEventListener('click', () => {
        painel.classList.toggle('hidden');
    });

    // Fechar o painel ao clicar fora
    document.addEventListener('click', (e) => {
        if (!sino.contains(e.target) && !painel.contains(e.target)) {
            painel.classList.add('hidden');
        }
    });

    // Buscar notificações
    fetch('/easy_m/app/view/pages/includes/notificacoes.php')
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                lista.innerHTML = '<li>Sem notificações nos próximos dias.</li>';
                contador.textContent = '0';
                contador.style.display = 'none';
                return;
            }

            lista.innerHTML = '';
            data.forEach(n => {
                const item = document.createElement('li');
                item.textContent = `📌 ${n.tipo}: ${n.nome} vence em ${n.dias} dia(s)`;
                lista.appendChild(item);
            });

            contador.textContent = data.length;
            contador.style.display = 'inline-block';
        })
        .catch(err => {
            console.error('Erro ao buscar notificações:', err);
            lista.innerHTML = '<li>Erro ao carregar notificações.</li>';
        });
});
