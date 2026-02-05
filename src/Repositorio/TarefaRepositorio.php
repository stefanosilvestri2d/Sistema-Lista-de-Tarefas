<?php

class TarefaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    // Formar um array de tarefas para as operações
    private function formarObjeto(array $dados): Tarefa
    {
        return new Tarefa(
        $dados['id'],
        $dados['nome'],
        (float) $dados['custo'],
        $dados['data_limite'],
        (int) $dados['ordem']
        );
    }

    //Função busca todas as tarefas para exibição
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM tarefas ORDER BY ordem";
        $statement = $this->pdo->query($sql);

        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todasAsTarefas = array_map(function ($tarefa) {
            return new Tarefa(
                $tarefa['id'],
                $tarefa['nome'],
                (float) $tarefa['custo'],
                $tarefa['data_limite'],
                (int) $tarefa['ordem']
            );
        }, $dados);

        return $todasAsTarefas;
    }
    // Para excluir tarefa e atualizar a ordem das outras tarefas
    public function deletar(int $id): void
    {
        try {
            
            $this->pdo->beginTransaction();

             
            $sqlDelete = "DELETE FROM tarefas WHERE id = ?";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->execute([$id]);

            //  Busca todas as tarefas restantes, ordenadas por 'ordem'
            $tarefas = $this->buscarTodos();

            //  Atualiza a ordem sequencialmente
            $sqlUpdate = "UPDATE tarefas SET ordem = ? WHERE id = ?";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);

            $novaOrdem = 1;
            foreach ($tarefas as $tarefa) {
                $stmtUpdate->execute([$novaOrdem, $tarefa->getId()]);
                $novaOrdem++;
            }

            
            $this->pdo->commit();
        } catch (PDOException $e) {
           
            $this->pdo->rollBack();
            throw $e; 
        }
    }

    //Função salvar tarefa, para ser usadas em outras operações
    public function salvar(Tarefa $tarefa): void
    {
        $sql = "INSERT INTO tarefas (nome, custo, data_limite, ordem)
                VALUES (?, ?, ?, ?)";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $tarefa->getNome());
        $statement->bindValue(2, $tarefa->getCusto());
        $statement->bindValue(3, $tarefa->getDataLimite());
        $statement->bindValue(4, $tarefa->getOrdem(), PDO::PARAM_INT);
        $statement->execute();
    }

    //Função que busca o id da tarefa para fazer manipulações
    public function buscar(int $id): Tarefa
    {
        $sql = "SELECT * FROM tarefas WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $id, PDO::PARAM_INT);
        $statement->execute();

        $dados = $statement->fetch(PDO::FETCH_ASSOC);

        return $this->formarObjeto($dados);
    }

    public function atualizar(Tarefa $tarefa): void
    {
        $sql = "UPDATE tarefas
                SET nome = ?, custo = ?, data_limite = ?
                WHERE id = ?";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $tarefa->getNome());
        $statement->bindValue(2, $tarefa->getCusto());
        $statement->bindValue(3, $tarefa->getDataLimite());
        $statement->bindValue(4, $tarefa->getId(), PDO::PARAM_INT);
        $statement->execute();
    }

    //Função para exibir custo total das tarefas
    public function somarCustos(): float
    {
        $sql = "SELECT SUM(custo) AS total_custo FROM tarefas";
        $statement = $this->pdo->query($sql);
        $resultado = $statement->fetch(PDO::FETCH_ASSOC);

        // Retorna 0 caso não haja tarefas
        return (float) ($resultado['total_custo'] ?? 0);
    }

    // Função para ser usada na lógica de reordenação com setas
    public function reordenar(int $id, string $direcao): void
    {
        $tarefas = $this->buscarTodos();

        // Encontra o índice da tarefa a ser movida
        $index = null;
        foreach ($tarefas as $i => $tarefa) {
            if ($tarefa->getId() === $id) {
                $index = $i;
                break;
            }
        }

        if ($index === null) return;

        // Define o índice da tarefa que vai trocar
        if ($direcao === 'up') {
            if ($index === 0) return; // já está no topo
            $trocaIndex = $index - 1;
        } elseif ($direcao === 'down') {
            if ($index === count($tarefas) - 1) return; // já está no final
            $trocaIndex = $index + 1;
        } else {
            return; // direção inválida
        }

        $tarefaAtual = $tarefas[$index];
        $tarefaTroca = $tarefas[$trocaIndex];

        $sql = "UPDATE tarefas SET ordem = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        // Passo 1: seta valores temporários para liberar UNIQUE
        $stmt->execute([-1, $tarefaAtual->getId()]);
        $stmt->execute([-2, $tarefaTroca->getId()]);

        // Passo 2: troca as ordens
        $stmt->execute([$tarefaTroca->getOrdem(), $tarefaAtual->getId()]);
        $stmt->execute([$tarefaAtual->getOrdem(), $tarefaTroca->getId()]);
    }

    public function buscarOrdemPorId(int $id): int
    {
        $sql = "SELECT ordem FROM tarefas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn();
    }

public function reordenarArrastar(int $idArrastada, int $idAlvo): void
{
    $arrastada = $this->buscar($idArrastada);
    $alvo      = $this->buscar($idAlvo);

    if (!$arrastada || !$alvo) return;

    $ordemOrigem  = $arrastada->getOrdem();
    $ordemDestino = $alvo->getOrdem();

    if ($ordemOrigem === $ordemDestino) return;

    try {
        $this->pdo->beginTransaction();

        // Temporariamente tira a arrastada da fila
        $tempOrdem = (count($this->buscarTodos()) + 1);
        $stmt = $this->pdo->prepare("UPDATE tarefas SET ordem = ? WHERE id = ?");
        $stmt->execute([$tempOrdem, $idArrastada]);

        // Determina intervalo e ajuste
        if ($ordemOrigem > $ordemDestino) {
            // 🔼 Subir: tarefas entre destino e origem-1 recebem +1
            $stmtIntervalo = $this->pdo->prepare(
                "SELECT id, ordem FROM tarefas
                 WHERE ordem >= ? AND ordem < ?
                 ORDER BY ordem DESC"
            );
            $stmtIntervalo->execute([$ordemDestino, $ordemOrigem]);

            foreach ($stmtIntervalo->fetchAll(PDO::FETCH_ASSOC) as $t) {
                $stmt->execute([$t['ordem'] + 1, $t['id']]);
            }

        } else {
            // 🔽 Descer: tarefas entre origem+1 e destino recebem -1
            $stmtIntervalo = $this->pdo->prepare(
                "SELECT id, ordem FROM tarefas
                 WHERE ordem > ? AND ordem <= ?
                 ORDER BY ordem ASC"
            );
            $stmtIntervalo->execute([$ordemOrigem, $ordemDestino]);

            foreach ($stmtIntervalo->fetchAll(PDO::FETCH_ASSOC) as $t) {
                $stmt->execute([$t['ordem'] - 1, $t['id']]);
            }
        }

        // Recoloca a arrastada na posição do alvo
        $stmt->execute([$ordemDestino, $idArrastada]);

        $this->pdo->commit();

    } catch (Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}




}

