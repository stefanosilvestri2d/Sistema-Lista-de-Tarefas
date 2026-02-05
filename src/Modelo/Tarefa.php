<?php

class Tarefa
{
    private ?int $id;           // ID da tarefa (pode ser null se ainda não cadastrada no BD)
    private string $nome;        // Nome da tarefa
    private float $custo;        // Custo associado à tarefa
    private string $dataLimite;  // Data limite em formato YYYY-MM-DD
    private int $ordem;          // Posição da tarefa em uma lista ordenada

    public function __construct(
        ?int $id,
        string $nome,
        float $custo,
        string $dataLimite,
        int $ordem
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->custo = $custo;
        $this->dataLimite = $dataLimite;
        $this->ordem = $ordem;
    }
     
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCusto(): float
    {
        return $this->custo;
    }

    public function getDataLimite(): string
    {
        return $this->dataLimite;
    }

    public function getOrdem(): int
    {
        return $this->ordem;
    }

    // Retorna o custo formatado no padrão brasileiro (R$ 1.234,56)
    public function getCustoFormatado(): string
    {
        return "R$ " . number_format($this->custo, 2, ',', '.');
    }

    // Retorna a data limite no formato dia/mês/ano
    public function getDataLimiteFormatada(): string
    {
        // strtotime converte a string para timestamp e date formata
        return date('d/m/Y', strtotime($this->dataLimite));
    }
}

