<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');

        // Adiciona headers CORS universais
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, Authorization, Content-Type, X-Requested-With, Accept');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Max-Age', '86400');

        // Log do IP remoto
        log_message('info', 'CORS request from IP: ' . $request->getIPAddress());

        // Responde imediatamente preflight requests (sem redirect)
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $response->setStatusCode(200)
                            ->setBody('CORS OK')
                            ->send();
        }

        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Reforça os headers CORS em todas as respostas
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, Authorization, Content-Type, X-Requested-With, Accept');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
