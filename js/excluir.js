document.addEventListener('DOMContentLoaded', () => {
  const botoesExcluir = document.querySelectorAll('.botao-excluir');
  const modal = document.getElementById('modal-excluir');
  const modalTexto = document.getElementById('modal-texto');
  const modalIdInput = document.getElementById('modal-id');
  const btnFechar = document.getElementById('btn-fechar');

  // Abre modal
  botoesExcluir.forEach(botao => {
    botao.addEventListener('click', () => {
      modalTexto.textContent = `Deseja excluir a tarefa "${botao.dataset.nome}"?`;
      modalIdInput.value = botao.dataset.id;
      modal.style.display = 'flex';
    });
  });

  // Fecha modal ao clicar em "Não"
  btnFechar.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Fecha modal ao clicar fora do conteúdo
  window.addEventListener('click', (event) => {
    if (event.target === modal) modal.style.display = 'none';
  });
});

