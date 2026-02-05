document.addEventListener('DOMContentLoaded', () => {
    const LIMIAR = 0.3; // Define a "zona" superior e inferior da célula para decidir se o arrasto deve trocar com a linha anterior ou seguinte

    function initRows() {
        document.querySelectorAll('.draggable-row').forEach(row => {
            row.draggable = true;

            // Início do arrasto: adiciona classe para estilo e marca a linha como arrastada
            row.addEventListener('dragstart', function () {
                this.classList.add('dragging');
                this._dragged = true; // marca para identificar a linha sendo arrastada
            });

            // Permite o drop
            row.addEventListener('dragover', e => e.preventDefault());

            // Drop: define para qual linha a arrastada deve se mover
            row.addEventListener('drop', function (e) {
                e.preventDefault();

                const dragged = document.querySelector('.dragging');
                if (!dragged || dragged === this) return;

                const bounding = this.getBoundingClientRect();
                const offset = e.clientY - bounding.top;

                // Lógica para decidir se a linha arrastada vai antes ou depois da linha alvo
                let targetRow = offset < bounding.height * LIMIAR
                                ? this.previousElementSibling ?? this // próximo acima
                                : offset > bounding.height * (1 - LIMIAR)
                                  ? this.nextElementSibling ?? this // próximo abaixo
                                  : this; // se estiver no meio, mantém posição

                // Se não houver linha válida ou for a mesma linha, não faz nada
                if (!targetRow || dragged.dataset.id === targetRow.dataset.id) return;

                // Atualiza backend via POST com os IDs da linha arrastada e do alvo
                fetch('reordenar_arrastar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        id_arrastada: dragged.dataset.id,
                        id_alvo: targetRow.dataset.id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Atualiza a tabela no frontend com a ordem correta do backend
                    if (data.success && data.tarefas) atualizarFrontend(data.tarefas);
                    else console.error('Erro ao reordenar:', data.msg);
                })
                .catch(console.error);

                // Remove a classe de arrasto de todas as linhas
                document.querySelectorAll('tr.dragging').forEach(tr => tr.classList.remove('dragging'));
            });

            // Final do arrasto: garante que a classe dragging seja removida
            row.addEventListener('dragend', () => row.classList.remove('dragging'));
        });
    }

    // Atualiza a tabela no frontend após reordenação no backend
    function atualizarFrontend(tarefas) {
        const tbody = document.querySelector('tbody');
        
        // Cria um mapeamento id -> <tr> para reaproveitar os elementos já existentes
        const map = Object.fromEntries([...tbody.querySelectorAll('tr')].map(tr => [tr.dataset.id, tr]));
        tbody.innerHTML = '';

        // Ordena as tarefas pelo campo ordem e reanexa as linhas correspondentes
        tarefas.sort((a, b) => a.ordem - b.ordem).forEach(t => {
            if (map[t.id]) tbody.appendChild(map[t.id]);
        });

        initRows(); // Reativa os eventos de drag nas linhas após re-render
    }

    initRows(); // Inicializa drag & drop nas linhas existentes
});
