<?php

require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

if (isset($_POST['editar'])) {

    $tarefaRepositorio = new TarefaRepositorio($pdo);

    try {
        $id = (int) $_POST['id'];

        // Busca a ordem atual
        $ordemAtual = $tarefaRepositorio->buscarOrdemPorId($id);

        $tarefa = new Tarefa(
            $id,
            $_POST['nome'],
            (float) $_POST['custo'],
            $_POST['data_limite'],
            $ordemAtual
        );

        // Tenta atualizar
        $tarefaRepositorio->atualizar($tarefa);

        // Sucesso
        echo "<script>
                alert('Tarefa atualizada com sucesso!');
                window.location.href = 'index.php';
              </script>";
        exit;

    } catch (PDOException $e) {

        // Nome duplicado
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Erro: já existe uma tarefa com esse nome.');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }

        // Qualquer outro erro
        echo "<script>
                alert('Erro ao atualizar tarefa.');
                window.location.href = 'index.php';
              </script>";
        exit;
    }
}
