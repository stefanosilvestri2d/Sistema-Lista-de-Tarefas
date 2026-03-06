<?php
require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

$tarefaRepositorio = new TarefaRepositorio($pdo);

// Verifica se o ID foi passado por GET
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$idTarefa = (int) $_GET['id'];

try {
    $tarefa = $tarefaRepositorio->buscar($idTarefa);
} catch (Exception $e) {
    echo "<script>alert('Tarefa não encontrada.'); window.location.href='index.php';</script>";
    exit;
}

// Processa o formulário de edição
if (isset($_POST['editar'])) {
    $nome = trim($_POST['nome']);
    $custo = (float) $_POST['custo'];
    $dataLimite = $_POST['data_limite'];

    $tarefaAtualizada = new Tarefa(
        $idTarefa,
        $nome,
        $custo,
        $dataLimite,
        $tarefa->getOrdem() // mantém a ordem original
    );

    try {
        $tarefaRepositorio->atualizar($tarefaAtualizada);
        echo "<script>
                alert('Tarefa atualizada com sucesso!');
                window.location.href='index.php';
              </script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>
                alert('Erro ao atualizar tarefa.');
                window.history.back();
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa</title>
    <link rel="stylesheet" href="css/editar-tarefa.css">
</head>
<body>

<div class="container_form">
    <h1>Editar Tarefa</h1>

    <form action="editar-tarefa.php?id=<?= $tarefa->getId() ?>" method="post" onsubmit="return validarFormulario()">
        <input type="hidden" name="id" value="<?= $tarefa->getId() ?>">

        <div class="form_grupo">
            <label class="form_label" for="nome">Nome da Tarefa</label>
            <input class="form_input" type="text" name="nome" id="nome" value="<?= htmlspecialchars($tarefa->getNome()) ?>" required>
        </div>

        <div class="form_grupo">
            <label class="form_label" for="custo">Custo (R$)</label>
            <input class="form_input" type="number" name="custo" id="custo" step="0.01" min="0" value="<?= $tarefa->getCusto() ?>" required>
        </div>

        <div class="form_grupo">
            <label class="form_label" for="data_limite">Data Limite</label>
            <input class="form_input" type="date" name="data_limite" id="data_limite" value="<?= $tarefa->getDataLimite() ?>" required>
        </div>

        <div class="submit">
            <button type="submit" name="editar" class="submit_btn">Salvar</button>
        </div>

        <a href="index.php" class="botao-voltar">← Voltar para Lista</a>
    </form>
</div>

<script>
function validarFormulario() {
    const nome = document.getElementById('nome').value.trim();
    const custo = document.getElementById('custo').value;
    const data = document.getElementById('data_limite').value;

    if (!nome || !custo || !data) {
        alert('Todos os campos são obrigatórios!');
        return false;
    }
    return true;
}

// Foca no campo 'nome' e posiciona o cursor no final
window.addEventListener('DOMContentLoaded', () => {
    const inputNome = document.getElementById('nome');
    if (inputNome) {
        inputNome.focus();
        const valor = inputNome.value;
        inputNome.value = '';
        inputNome.value = valor;
    }
});
</script>

</body>
</html>