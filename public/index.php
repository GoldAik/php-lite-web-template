<?php

declare(strict_types = 1);

define('DEBUG_MODE', true);
define('SOURCE_PATH', __DIR__ . '/../src');
define('ENTITY_PATH', SOURCE_PATH . '/entity');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$env = Dotenv::createImmutable(SOURCE_PATH . '/..');
$env->load();

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [ENTITY_PATH],
    isDevMode: DEBUG_MODE
);

$connection = DriverManager::getConnection([
    'driver' => $_ENV['DATABASE_DRIVER'],
    'dbname' => $_ENV['DATABASE_NAME'],
    'user' => $_ENV['DATABASE_USER'],
    'password' => $_ENV['DATABASE_PASSWORD'],
], $config);

$app = \DI\Bridge\Slim\Bridge::create();
$app->addRoutingMiddleware();

$app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

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