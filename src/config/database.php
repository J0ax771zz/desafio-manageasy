<?php

/* 
Neste arquivo criamos nossa conexão com o banco de dados utilizando a biblioteca mysqli
Utilizamos como paramêtros de conexão as variáveis de ambiente localizadas no nosso .env
*/

require_once __DIR__ ."/bootstrap.php";

class Database{
    public $db;
    public function connect(){

        $this->db = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_NAME'],
            $_ENV['DB_PORT']
        );

        if($this->db->connect_error) {
            http_response_code(500);
            echo json_encode([
                "error" => true,
                "message" => "Erro ao conectar ao banco de dados"
            ]);
            exit;
        }

        return $this->db;

    }
}