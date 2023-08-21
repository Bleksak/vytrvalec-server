<?php

namespace App\Controller;

use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use ReflectionException;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class API extends AbstractController
{
    #[Route('/apidoc/{selection}', name: 'APIDoc', methods: ['GET'], env: 'dev')]
    public function api(RouterInterface $router, string $selection = ''): Response
    {
        return $this->render('api.html.twig', [
            'routes' => $this->collectApiRoutes($router),
            'selection' => $selection
        ]);
    }

    /**
     * @param RouterInterface $router
     * @return array<int|string, array<string, array<string, string>>>
     */
    private function collectApiRoutes(RouterInterface $router): array
    {
        $routes = [];

        // Get all the defined routes
        $routesCollection = $router->getRouteCollection();

        // Loop through the routes and check for the ApiRoute annotation
        foreach ($routesCollection as $name => $route) {
            try {
                $default = $route->getDefault('_controller');

                $method = new ReflectionMethod($default);
                $declaringClass = $method->getDeclaringClass();

                $classAttributes = $declaringClass->getAttributes(ApiResource::class);

                if(!empty($classAttributes)) {
                    $classArgs = $classAttributes[0]->getArguments();
                    $resourceName = $classArgs['resourceName'] ?? $classArgs[0];
                } else {
                    $resourceName = $declaringClass->getName();
                }

                $attrs = $method->getAttributes(ApiRoute::class);

                if(empty($attrs)) continue;

                $apiRouteAttribute = $attrs[0]->newInstance();

                $routes[$resourceName][$name]['path'] = $apiRouteAttribute->getFakePath();
                $routes[$resourceName][$name]['name'] = $apiRouteAttribute->getFakeName();
                $routes[$resourceName][$name]['documentation'] = $apiRouteAttribute->getDocumentation();
                $routes[$resourceName][$name]['method'] = $apiRouteAttribute->getMethod();

                $scheme = $apiRouteAttribute->getRequestScheme();
                $responses = $apiRouteAttribute->getResponses();
                if($scheme !== null) {
                    $routes[$resourceName][$name]['scheme'] = $scheme;
                }

                if($responses !== null) {
                    $routes[$resourceName][$name]['responses'] = $responses;
                }
            }
            catch(ReflectionException) {
            }
        }

        return $routes;
    }
}