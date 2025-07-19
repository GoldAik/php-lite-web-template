<?php

declare(strict_types = 1);

namespace App\Routes;

use Doctrine\ORM\EntityManager;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;

use App\Entities\User;

return function (\Slim\App $app, EntityManager $entityManager, $args = []) {
    $app->group('/profile/{name}', function (RouteCollectorProxy $group) use ($entityManager, $args) {
        
        $group->get('', function (Response $response, $name) use ($entityManager) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);

            if (!$user) {
                $response->getBody()->write('There is no such profile');
                return $response;
            }

            $response->getBody()->write("Welcome on profile $name <br>");
            $response->getBody()->write(nl2br(json_encode($user->getData(), JSON_PRETTY_PRINT)));

            return $response;
            $response->getBody()->write("profile page - $name");
            return $response;
        });


        $group->get('/delete', function (Response $response, $name) use ($entityManager) {
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
        })->add(\App\Middlewares\TokenMiddleware::class);

    });
};