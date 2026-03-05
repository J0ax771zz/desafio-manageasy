<?php

/* 
A classe Contato é o nosso model, ela tem como responsabilidade executar as querys
utilizando Prepared Statetments evitando o risco de SQL Injection em nosso código
Ela contém métodos como criar contato, localizar contato com páginação de resultados, atualizar contatos
e deletar contatos
*/
class Contato {
    private $conn;
    private $table = "contato";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createContact($data) {
        $sql = "INSERT INTO {$this->table}
        (nome, email, telefone, data_nascimento)
        VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssss",
            $data["nome"],
            $data["email"],
            $data["telefone"],
            $data["data_nascimento"]
        );

        return $stmt->execute();
    }

    public function findAll($filter = null, $page = 1, $limit = 10) {
        
        $offset = ($page -1) * $limit;
        $sql = "SELECT * FROM {$this->table} ";

        if($filter){
            $sql .= "WHERE nome LIKE ? OR email LIKE ?";
        }

        $sql .= " ORDER BY nome ASC, created_at ASC";
        $sql .= " LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);

        if(!$stmt){
            die("Erro SQL " .$this->conn->error);
        }

        if($filter){
            $search = "%{$filter}%";
            $stmt->bind_param("ssii", $search, $search, $limit, $offset);
        }else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll($filter = null) {

    $sql = "SELECT COUNT(*) as total FROM {$this->table}";

    if ($filter) {
        $sql .= " WHERE nome LIKE ? OR email LIKE ?";
    }

    $stmt = $this->conn->prepare($sql);

    if ($filter) {
        $search = "%{$filter}%";
        $stmt->bind_param("ss", $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result["total"];
}

    public function updateContact($data) {
        $sql = "UPDATE {$this->table}
        SET nome = ?, email = ?, telefone = ?, data_nascimento = ?
        WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssi",
            $data["nome"],
            $data["email"],
            $data["telefone"],
            $data["data_nascimento"],
            $data["id"]
        );
        return $stmt->execute();
    }

    public function deleteContact($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}