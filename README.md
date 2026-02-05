# Sistema de Lista de Tarefas

Sistema web desenvolvido em PHP para gerenciamento de tarefas, permitindo cadastrar, editar, excluir e reordenar itens de forma simples e intuitiva, utilizando setas e drag and drop.

---

## ✨ Funcionalidades

- Cadastro de tarefas com nome, custo e data limite
- Edição e exclusão de tarefas por meio de modais
- Reordenação de tarefas:
  - Movimentação por setas (subir e descer)
  - Arrastar e soltar (drag and drop)
- Cálculo automático do custo total das tarefas

---

## 🛠 Tecnologias Utilizadas

- PHP 8.2
- MySQL
- HTML
- CSS
- JavaScript
- PDO para acesso seguro ao banco de dados

---

## 📁 Estrutura do Projeto

/css  
→ Arquivos de estilo

/js  
→ Scripts de interação (modais, drag and drop e setas)

/src/Modelo  
→ Classe responsável pela entidade **Tarefa**

/src/Repositorio  
→ Classe responsável pelas operações no banco de dados

/src/conexao-bd.php  
→ Arquivo de conexão com o banco de dados

index.php  
→ Página principal do sistema

cadastrar-tarefa.php  
→ Cadastro de novas tarefas

editar-tarefa.php  
→ Edição de tarefas existentes

excluir-tarefa.php  
→ Exclusão de tarefas

---

## 🧭 Como Utilizar

- Utilize o botão **Incluir Tarefa** para cadastrar uma nova tarefa.
- Para editar ou excluir, use os botões correspondentes na listagem.
- A ordem das tarefas pode ser alterada utilizando as setas ou arrastando o item desejado.
- O custo total das tarefas é atualizado automaticamente.



## 👤 Autor

Desenvolvido por **Stefano Silvestri**.
