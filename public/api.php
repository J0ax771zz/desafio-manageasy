<?php

/*
Este arquivo recebe todas as requisições HTTP (GET, POST, PUT, DELETE)
e delega para o ContatoController, que executa as operações de CRUD
no banco de dados via ContatoService e Contato model.

Também configura headers para respostas JSON e permite requisições AJAX.
*/

header("Content-Type: application/json");

// Permitir requisições AJAX (opcional para desafio)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Corrige caminho das pastas (porque agora está dentro de /public)
require_once __DIR__ . "/../src/config/database.php";
require_once __DIR__ . "/../src/models/Contato.php";
require_once __DIR__ . "/../src/services/ContatoService.php";
require_once __DIR__ . "/../src/controllers/ContatoController.php";

try {

    // Conexão com banco
    $db = (new Database())->connect();

    // Injeção de dependência
    $model = new Contato($db);
    $service = new ContatoService($model);
    $controller = new ContatoController($service);

    $method = $_SERVER["REQUEST_METHOD"];

    switch ($method) {

        case "GET":
            $controller->index();
            break;

        case "POST":
            $controller->create();
            break;

        case "PUT":
            $controller->update();
            break;

        case "DELETE":
            $controller->delete();
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
            break;
    }

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "error" => "Erro interno do servidor"
        // Em produção não mostramos $e->getMessage()
    ]);
}