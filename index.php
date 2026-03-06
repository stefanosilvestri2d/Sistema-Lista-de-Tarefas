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
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/modal.css">
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
          <th>Id</th>
          <th></th> <!-- Editar -->
          <th></th> <!-- Excluir -->
          <th></th> <!-- Setas -->
        </tr>
      </thead>
      <tbody>
<?php foreach($tarefas as $tarefa): 
    $custo = (float) $tarefa->getCusto();
    $classe = $custo >= 1000 ? 'tarefa-cara' : '';
?>
<tr class="draggable-row <?= $classe ?>" draggable="true" data-id="<?= $tarefa->getId() ?>">
    <td><?= htmlspecialchars($tarefa->getNome()) ?></td>
    <td><?= $tarefa->getCustoFormatado() ?></td>
    <td><?= $tarefa->getDataLimiteFormatada() ?></td>
    <td><?= $tarefa->getId() ?></td>
    <td>
        <a href="editar-tarefa.php?id=<?= $tarefa->getId() ?>" class="botao-editar-modal">
            Editar
        </a>
    </td>
    <td>
        <!-- Botão de excluir, mantém modal de confirmação -->
        <button type="button" class="botao-excluir" data-id="<?= $tarefa->getId() ?>" data-nome="<?= htmlspecialchars($tarefa->getNome()) ?>">Excluir</button>
    </td>
    <td>
        <!-- Setas para mover tarefas -->
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
          <td></td>
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


<script src="js/arrastar.js"></script>
<script src="js/setas.js"></script> 
<script src="js/excluir.js"></script>

</body>
</html>