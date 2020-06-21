<?php

require __DIR__ . '/../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

$rotas = require __DIR__ . '/../config/routes.php';
$caminho = $_SERVER['PATH_INFO'];

if (!array_key_exists($caminho, $rotas)) {
    http_response_code(404);
    exit();
}

session_start();

$ehRotaDeLogin = stripos($caminho, 'login');

if (!isset($_SESSION['logado']) && $ehRotaDeLogin === false) {
    header('Location: /login');
    exit();
}

/**
 * Adicionamos ao projeto via composer as definicoes da PSR7. Com isso temos as interfaces
 * que definem as Requests e Responses de forma padronizada
 * composer require psr/http-message
 *
 * Depois adicionamos ao projeto uma implementacao da PSR7. Usaremos o pacote Nyholm.
 * Esse pacote implementa a PSR-7 e a PSR-17 que define a criação de Requests
 * composer require nyholm/psr7
 *
 * Para termos a implementação de uma fábrica de mensagens HTTP adicionaremos mais um pacote
 * composer require nyholm/psr7-server
 *
 * Finalmente criamos a Request com a Fabrica vinda no pacote do Nyholm
 */

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$request = $creator->fromGlobals();

$classeControladora = $rotas[$caminho];

/**
 * Uso do PSR-15 utilizando a RequestHandlerInterface como interface controladora
 * Para isso adicionamos o composer require psr/http-server-handler que nos fornece a
 * interface RequestHandlerInterface. Essa interface será implementada em todos os Controllers
 * da aplicação e com isso padronizamos o sistema
 */
/** @var RequestHandlerInterface $controlador */
$controlador = new $classeControladora();

/** @var ResponseInterface $response */
$response = $controlador->handle($request);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();