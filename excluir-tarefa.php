<?php

require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

// Remove a tarefa do banco a partir do ID enviado pelo formulário de exclusão
if (isset($_POST['id'])) {
    $tarefaRepositorio = new TarefaRepositorio($pdo);
    $tarefaRepositorio->deletar((int) $_POST['id']);
}

// Após a exclusão, retorna para a lista principal
header("Location: index.php");
exit;
