<?php


/* 
A classe ContatoService é responsavel por cuidar das regras de negócio da aplicação
antes que qualquer dado chegue ao banco de dados ele é validado e se passar nos testes
é incluido no banco de dados
*/
class ContatoService {

    private $contatoModel;

    public function __construct($model) {
        $this->contatoModel = $model;
    }

    public function create($data) {

        // Validação
        if (empty($data["nome"]) || strlen($data["nome"]) < 3) {
            throw new Exception("Nome inválido.");
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido.");
        }

        if (empty($data["telefone"])|| strlen($data["telefone"]) < 9) {
            throw new Exception("Telefone é obrigatório.");
        }
        
        $data["nome"] = trim($data["nome"]);
        $data["email"] = trim($data["email"]);

        return $this->contatoModel->createContact($data);
    }

    public function list($filter = null, $page = 1, $limit = 10) {

        if ($page < 1) {
            $page = 1;
        }

        if ($limit < 1 || $limit > 100) {
            $limit = 10;
        }

        $data = $this->contatoModel->findAll($filter, $page, $limit);
        $total = $this->contatoModel->countAll($filter);

        return [
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "totalPages" => ceil($total / $limit)
        ];
    }

    public function update($data) {

        if (empty($data["id"])) {
            throw new Exception("ID obrigatório.");
        }

        if (!is_numeric($data["id"])) {
            throw new Exception(message: "ID inválido.");
        }

        return $this->contatoModel->updateContact($data);
    }

    public function delete($id) {

        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        return $this->contatoModel->deleteContact($id);
    }
}