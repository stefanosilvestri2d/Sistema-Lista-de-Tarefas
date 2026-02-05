<?php
require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

$tarefaRepositorio = new TarefaRepositorio($pdo);

// Busca todas as tarefas para exibir na tabela
$tarefas = $tarefaRepositorio->buscarTodos();

// Calcula o custo total de todas as tarefas para exibir no rodapé da tabela
$totalCusto = $tarefaRepositorio->somarCustos() ?? 0;
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
  <title>Sistema Lista de Tarefas</title>
  <link rel="stylesheet" href="css/modal.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<main>
  <h2>Lista de Tarefas</h2>

  <section class="container-table">
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Custo</th>
          <th>Data Limite</th>
          <th></th> <!-- Editar -->
          <th></th> <!-- Excluir -->
          <th></th> <!-- Setas -->
        </tr>
      </thead>
      <tbody>
<?php foreach($tarefas as $tarefa): 
    $custo = (float) $tarefa->getCusto();
    // Destaca tarefas caras (>=1000) com uma classe CSS especial
    $classe = $custo >= 1000 ? 'tarefa-cara' : '';
?>
<tr class="draggable-row <?= $classe ?>" draggable="true" data-id="<?= $tarefa->getId() ?>">
    <td><?= htmlspecialchars($tarefa->getNome()) ?></td>
    <td><?= $tarefa->getCustoFormatado() ?></td>
    <td><?= $tarefa->getDataLimiteFormatada() ?></td>
    <td>
        <!-- Botão de editar com dados da tarefa armazenados em data-atributos para popular o modal -->
        <button 
            type="button" 
            class="botao-editar-modal"
            data-id="<?= $tarefa->getId() ?>"
            data-nome="<?= htmlspecialchars($tarefa->getNome()) ?>"
            data-custo="<?= htmlspecialchars($tarefa->getCusto()) ?>"
            data-data="<?= htmlspecialchars($tarefa->getDataLimite()) ?>"
        >
            Editar
        </button>
    </td>
    <td>
        <!-- Botão de excluir, abre modal de confirmação -->
        <button type="button" class="botao-excluir" data-id="<?= $tarefa->getId() ?>" data-nome="<?= htmlspecialchars($tarefa->getNome()) ?>">Excluir</button>
    </td>
    <td>
        <!-- Setas para mover tarefas para cima ou para baixo -->
        <button type="button" class="seta-cima" data-id="<?= $tarefa->getId() ?>">↑</button>
        <button type="button" class="seta-baixo" data-id="<?= $tarefa->getId() ?>">↓</button>
    </td>
</tr>
<?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td><strong>Custo total das tarefas</strong></td>
          <td><strong><?= "R$ ".number_format($totalCusto, 2, ',', '.') ?></strong></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td> <!-- Espaço para coluna das setas -->
        </tr>
      </tfoot>
    </table>
    <a class="botao-cadastrar" href="cadastrar-tarefa.php">Incluir Tarefa</a>
  </section>
</main>

<!-- Modal de exclusão -->
<div id="modal-excluir" class="modal" style="display:none;">
  <div class="modal-conteudo">
    <p id="modal-texto">Deseja excluir esta tarefa?</p>
    <form id="modal-form" action="excluir-tarefa.php" method="post">
      <!-- Guarda o ID da tarefa a ser excluída -->
      <input type="hidden" name="id" id="modal-id" value="">
      <div style="display:flex; gap:1em;">
        <button type="submit" style="background-color:#e74c3c;">Sim</button>
        <button type="button" id="btn-fechar" style="background-color:#3498db;">Não</button>
      </div>
    </form>
  </div>
</div>

<script src="js/excluir.js"></script>

<!-- Modal de edição -->
<div id="modal-editar" class="modal" style="display:none;">
  <div class="modal-conteudo">
    <h3>Editar Tarefa</h3>
    <form id="form-editar" action="editar-tarefa.php" method="post" onsubmit="return validarModalEditar()">
      <input type="hidden" name="id" id="editar-id">
      
      <div class="form_grupo">
        <label for="editar-nome">Nome da Tarefa</label>
        <input type="text" name="nome" id="editar-nome" required>
      </div>

      <div class="form_grupo">
        <label for="editar-custo">Custo (R$)</label>
        <input type="number" name="custo" id="editar-custo" step="0.01" min="0" required>
      </div>

      <div class="form_grupo">
        <label for="editar-data">Data Limite</label>
        <input type="date" name="data_limite" id="editar-data" required>
      </div>

      <div style="display:flex; gap:1em; margin-top:1em;">
        <button type="submit" name="editar" style="background-color:#2ecc71;">Salvar</button>
        <button type="button" id="btn-fechar-editar" style="background-color:#3498db;">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="js/editarTarefa.js"></script>
<script src="js/arrastar.js"></script>
<script src="js/setas.js"></script> 

</body>
</html>
