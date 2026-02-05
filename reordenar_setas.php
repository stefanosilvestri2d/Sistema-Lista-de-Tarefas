<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Ativa exibição de todos os erros para depuração

require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

$tarefaRepositorio = new TarefaRepositorio($pdo);

// Recebe os parâmetros via GET (id da tarefa e direção da reordenação)
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$direcao = isset($_GET['direcao']) ? $_GET['direcao'] : null;

// Verifica se os parâmetros são válidos antes de tentar reordenar
if ($id && ($direcao === 'up' || $direcao === 'down')) {
    try {
        // Chama a função que move a tarefa para cima ou para baixo na lista
        $tarefaRepositorio->reordenar($id, $direcao);
    } catch (PDOException $e) {
        // Interrompe a execução em caso de erro e exibe a mensagem
        die("Erro ao reordenar tarefa: " . $e->getMessage());
    }
} else {
    // Parâmetros inválidos ou faltando
    die("Parâmetros inválidos.");
}

// Redireciona de volta para a página principal da lista de tarefas
header("Location: index.php");
exit;
