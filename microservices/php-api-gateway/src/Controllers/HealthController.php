<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class HealthController
{
    public function check(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response(200);
        $response->getBody()->write(json_encode([
            'status' => 'healthy',
            'service' => 'php-api-gateway',
            'timestamp' => date('c')
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}