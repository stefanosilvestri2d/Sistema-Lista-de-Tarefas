<?php
session_start();
header('Content-Type: application/json'); // Define que a resposta será JSON

require "src/conexao-bd.php";
require "src/Modelo/Tarefa.php";
require "src/Repositorio/TarefaRepositorio.php";

// Verifica se os parâmetros necessários foram enviados e se são numéricos
if (
    !isset($_POST['id_arrastada'], $_POST['id_alvo']) ||
    !is_numeric($_POST['id_arrastada']) ||
    !is_numeric($_POST['id_alvo'])
) {
    // Retorna erro caso os parâmetros sejam inválidos
    echo json_encode([
        'success' => false,
        'msg' => 'Parâmetros inválidos'
    ]);
    exit;
}

$idArrastada = (int) $_POST['id_arrastada'];
$idAlvo      = (int) $_POST['id_alvo'];

try {
    $repo = new TarefaRepositorio($pdo);

    // Chama a função que reordena tarefas com base no drag and drop
    $repo->reordenarArrastar($idArrastada, $idAlvo);

    // Busca todas as tarefas atualizadas para enviar de volta ao front-end
    $tarefas = $repo->buscarTodos();

    // Prepara os dados das tarefas para JSON, incluindo valores formatados para exibição
    $tarefasJson = array_map(fn($t) => [
        'id' => $t->getId(),
        'ordem' => $t->getOrdem(),
        'nome' => $t->getNome(),
        'custo' => $t->getCusto(),
        'custo_formatado' => $t->getCustoFormatado(), // valor já formatado para exibição
        'data_limite' => $t->getDataLimite(),
        'data_limite_formatada' => $t->getDataLimiteFormatada(), // data no formato brasileiro
    ], $tarefas);

    echo json_encode([
        'success' => true,
        'tarefas' => $tarefasJson
    ]);

} catch (Throwable $e) {
    // Em caso de erro, retorna código HTTP 500 e mensagem de erro genérica
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'msg' => 'Erro ao reordenar tarefas'
    ]);
}
