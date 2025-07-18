<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Doctrine\ORM\EntityManager;
use App\Entities\User;

/**
 * @var Slim\App $app
 */
$app = require_once __DIR__ . '/../bootstrap/application.php';

$middleware = function (Request $req, RequestHandler $reqHandler) {
    $res = $reqHandler->handle($req);
    return $res;
};

/**
 * @var EntityManager $entityManager
 */
$app->get('/profile/{name}', function (Response $response, $name) use ($entityManager) {

    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);

    if (!$user) {
        $response->getBody()->write('There is no such profile');
        return $response;
    }

    $response->getBody()->write("Welcome on profile $name <br>");
    $response->getBody()->write(json_encode($user->getData()));

    return $response;
})->add($middleware);


$app->get('/profile/{name}/delete', function (Response $response, $name) use ($entityManager) {

    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);

    if (!$user) {
        $response->getBody()->write('There is no such profile');
        return $response;
    }

    $entityManager->remove($user);
    $entityManager->flush();

    $response->getBody()->write("Profile was remove");
    $response->getBody()->write(json_encode($user->getData()));

    return $response;
})  ->setName('delete-profile')
    ->add(App\Middlewares\TokenMiddleware::class)    
    ;

$app->run();