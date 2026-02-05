document.addEventListener('DOMContentLoaded', () => {
    const tabela = document.querySelector('tbody');

    // Função para mover a linha visualmente e chamar o backend
    function moverLinha(row, direcao) {
        if (!row) return;
        let alvo = null;

        if (direcao === 'up') {
            alvo = row.previousElementSibling;
            if (alvo) {
                tabela.insertBefore(row, alvo);
            }
        } else if (direcao === 'down') {
            alvo = row.nextElementSibling;
            if (alvo) {
                tabela.insertBefore(alvo, row);
            }
        }

        // Atualiza o backend via GET
        const id = row.dataset.id;
        if (alvo) {
            fetch(`reordenar_setas.php?id=${id}&direcao=${direcao}`)
                .then(res => {
                    if (!res.ok) console.error('Erro ao atualizar ordem no backend');
                })
                .catch(err => console.error('Erro na requisição:', err));
        }
    }

    // Captura clique nas setas
    tabela.addEventListener('click', (e) => {
        const row = e.target.closest('tr');
        if (!row) return;

        if (e.target.classList.contains('seta-cima')) {
            moverLinha(row, 'up');
        } else if (e.target.classList.contains('seta-baixo')) {
            moverLinha(row, 'down');
        }
    });
});
