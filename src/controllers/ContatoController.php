<?php

/* 
A classe ContatoController é responsável por tratar as requisiçoes HTTP que chegam a nossa API
Recebendo os dados, fazendo validações e entregando as respostas via HTTP/JSON
*/
class ContatoController
{
    private $service;
    public function __construct($service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $filter = $_GET["search"] ?? null;
        $page = $_GET["page"] ?? 1;
        $limit = $_GET["limit"] ?? 10;

        $result = $this->service->list($filter, $page, $limit);

        echo json_encode($result);
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $this->service->create($data);

            echo json_encode([
                "message" => "Contato criado com sucesso."
            ]);

        } catch (Exception $e) {

            http_response_code(400);

            echo json_encode([
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $_GET["id"] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID obrigatório na URL."]);
            return;
        }

        $data["id"] = $id;

        try {
            $this->service->update($data);

            echo json_encode([
                "message" => "Contato atualizado com sucesso."
            ]);

        } catch (Exception $e) {

            http_response_code(400);

            echo json_encode([
                "error" => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        try {
            $id = $_GET["id"] ?? null;
            $this->service->delete($id);
            $this->response(200, ["message" => "Contato removido com sucesso"]);

        } catch (Exception $ex) {
            $this->response(400, ["error" => $ex->getMessage()]);
        }
    }

    private function response($code, $data)
    {
        http_response_code($code);
        header("Content-Type: application/json");

        echo json_encode($data);
        exit;
    }
}