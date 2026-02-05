document.addEventListener('DOMContentLoaded', () => {
    const modalEditar = document.getElementById('modal-editar');
    const btnFecharEditar = document.getElementById('btn-fechar-editar');

    const formId = document.getElementById('editar-id');
    const formNome = document.getElementById('editar-nome');
    const formCusto = document.getElementById('editar-custo');
    const formData = document.getElementById('editar-data');

    // Ao clicar em um botão de editar, abre o modal e preenche os campos com os dados da tarefa
    document.querySelectorAll('.botao-editar-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            // Preenche os campos do formulário com os atributos data-* do botão
            formId.value = btn.dataset.id;
            formNome.value = btn.dataset.nome;
            formCusto.value = btn.dataset.custo;
            formData.value = btn.dataset.data;

            // Exibe o modal
            modalEditar.style.display = 'flex';
        });
    });

    // Fecha o modal ao clicar no botão "Cancelar"
    btnFecharEditar.addEventListener('click', () => {
        modalEditar.style.display = 'none';
    });
});

// Validação do modal de edição antes de enviar
function validarModalEditar() {
    const nome = document.getElementById('editar-nome').value.trim();
    const custo = document.getElementById('editar-custo').value;
    const data = document.getElementById('editar-data').value;

    // Garante que todos os campos obrigatórios estejam preenchidos
    if (!nome || !custo || !data) {
        alert('Todos os campos são obrigatórios!');
        return false;
    }

    return true;
}
