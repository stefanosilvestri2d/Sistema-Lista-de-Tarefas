<?php

require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

if (isset($_POST['cadastro'])) {

    $tarefaRepositorio = new TarefaRepositorio($pdo);

    // Busca todas as tarefas para determinar a maior ordem existente
    // Isso garante que a nova tarefa sempre fique no final da lista
    $tarefasExistentes = $tarefaRepositorio->buscarTodos();
    $maiorOrdem = 0;
    foreach ($tarefasExistentes as $t) {
        if ($t->getOrdem() > $maiorOrdem) {
            $maiorOrdem = $t->getOrdem();
        }
    }

    // Define a ordem da nova tarefa como a última posição
    $ordemNovaTarefa = $maiorOrdem + 1;

    // Cria o objeto Tarefa com os dados do formulário
    $tarefa = new Tarefa(
        null,
        $_POST['nome'],
        (float) $_POST['custo'],
        $_POST['data_limite'],
        $ordemNovaTarefa
    );

    try {
        $tarefaRepositorio->salvar($tarefa);

        // Feedback para o usuário e redirecionamento
        echo "<script>
                alert('Tarefa cadastrada com sucesso!');
                window.location.href = 'index.php';
              </script>";
        exit;

    } catch (PDOException $e) {

        // Tratamento específico para erros de duplicidade (chave única)
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Erro: já existe uma tarefa com esse nome.');
                    window.history.back();
                  </script>";
            exit;
        }

        // Qualquer outro erro genérico
        echo "<script>
                alert('Erro ao cadastrar tarefa.');
                window.history.back();
              </script>";
        exit;
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tarefa</title>
    <link rel="stylesheet" href="css/cadastrar-tarefa.css">
</head>
<body>

<div class="container_form">

    <h1>Nova Tarefa</h1>

    <!-- Formulário de cadastro de tarefa -->
    <form class="form" action="cadastrar-tarefa.php" method="post" onsubmit="return validarFormulario()">

        <div class="form_grupo">
            <label for="nome" class="form_label">Nome da Tarefa</label>
            <input type="text" name="nome" id="nome" class="form_input" required>
        </div>

        <div class="form_grupo">
            <label for="custo" class="form_label">Custo (R$)</label>
            <input type="number" name="custo" id="custo" class="form_input" step="0.01" min="0" required>
        </div>

        <div class="form_grupo">
            <label for="data_limite" class="form_label">Data Limite</label>
            <input type="date" name="data_limite" id="data_limite" class="form_input" required>
        </div>

        <div class="submit">
            <button type="submit" name="cadastro" class="submit_btn">Salvar Tarefa</button>
        </div>

    </form>

    <a href="index.php" class="botao-voltar">← Voltar para Lista</a>

</div>

<script>
// Validação simples para evitar envio de formulário com campos vazios ou inválidos
function validarFormulario() {
    const nome = document.getElementById('nome').value.trim();
    const custo = document.getElementById('custo').value;
    const data = document.getElementById('data_limite').value;

    if (!nome || !custo || !data) {
        alert('Todos os campos são obrigatórios e não podem estar vazios!');
        return false;
    }
    return true;
}
</script>

</body>
</html>
