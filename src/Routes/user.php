<?php

declare(strict_types = 1);

namespace App\Routes;

use Doctrine\ORM\EntityManager;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;

use App\Entity\User;

return function (\Slim\App $app, $args = []) {
    $container = $app->getContainer();
    $entityManager = $container->get(EntityManager::class);

    $app->group('/profile/{name}', function (RouteCollectorProxy $group) use ($entityManager, $args) {
        
        $group->get('', function (Response $response, $name) use ($entityManager) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);
            $userData = $user?->getData() ?? ['error' => 'There is no such profile'];

            $response->getBody()->write(json_encode($userData, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type','application/json');
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
        });
    });
};