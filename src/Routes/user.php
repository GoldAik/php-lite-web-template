<?php

declare(strict_types = 1);

namespace App\Routes;

use Doctrine\ORM\EntityManager;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Entity\User;
use Slim\Views\Twig;

return function (\Slim\App $app, $args = []) {
    $container = $app->getContainer();
    $entityManager = $container->get(EntityManager::class);

    $app->group('/profile/{name}', function (RouteCollectorProxy $group) use ($entityManager, $args) {
        
        $group->get('', function (Request $request, Response $response, $name) use ($entityManager) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $name]);

            $userData = $user?->getData() ?? [];

            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'profile.html.twig', ['user' => $userData]);
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