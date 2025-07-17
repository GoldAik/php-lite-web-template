<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

$app = require_once __DIR__ . '/../bootstrap/application.php';

$middleware = function (Request $req, RequestHandler $reqHandler) {
    $res = $reqHandler->handle($req);
    return $res;
};

$app->get('/{name}', function (Response $response, $name) use ($connection) {

    $stmt = $connection->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', $name);
    $stmt = $stmt->executeQuery();
    $result = $stmt->fetchAssociative();

    if (!$result) {
        $response->getBody()->write('There is no such profile');
        return $response;
    }

    $response->getBody()->write("Welcome on profile $name <br>");
    $response->getBody()->write(json_encode($result));

    return $response;
})->add($middleware);

$app->run();