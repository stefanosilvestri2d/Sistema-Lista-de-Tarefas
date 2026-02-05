<?php

// Cria a conexão com o banco de dados
// Essa conexão será usada pelo front-end ou pelo repositório de tarefas
$pdo = new PDO(
    'mysql:host=localhost;dbname=lista_tarefas;charset=utf8', // DSN: host, nome do banco e charset
    '',  // usuário do banco
    ''  // senha do banco
);
