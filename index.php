<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host'] = "localhost";
$config['db']['port'] = "3306";
$config['db']['user'] = "root";
$config['db']['pass'] = "p@55w0rd";
$config['db']['dbname'] = "test";


$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('logger');
    $file_handler = new \Monolog\Handler\StreamHandler("./logs/app.log");
    $logger->pushHandler($file_handler);
    $logger->showMsg = 'Logger\'s Message';
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ":" . $db['host']
            . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->group('/hello', function() use ($app) {
    $mwName = function ($request, $response, $next) {
        $response->getBody()->write('<p>Hello</p>');
        $response = $next($request, $response);
        $response->getBody()->write(
                '<div style="width:60%;text-align:right;">'
                . date('Y-m-d H:i:s') . '</div>');

        return $response;
    };
    $app->get('/name/{name}', function(Request $request, Response $response) {
        
        if($request->hasHeader('user_agent')){
            echo implode("<br/> ", $request->getHeader('user_agent'));
        }  else {
            echo 'No user agent!';
        }
        echo '<br/>';
        
        $name = $request->getAttribute('name');
        $response->getBody()->write('Hi, ' . $name . '<br/>');

        if ($this->has('logger')) {
            $getContainer = $this->logger;
            var_dump($getContainer->showMsg);
        } else {
            var_dump('No Logger');
        }
        //var_dump($getContainer);
        $this->logger->addInfo("Say hello to a name");
        return $response;
    })->add($mwName);
    $app->get('/time', function(Request $request, Response $response) {

        $response = $response->withAddedHeader('Allow', 'OPTION');
        
        echo('<h3>Request</h3>');
        $headers = $request->getHeaders();
        foreach ($headers as $name => $values) {
            echo '<br/>'. $name . ": " . implode("<br/> ", $values);
        }
        
        echo('<h3>Response</h3>');
        $headers = $response->getHeaders();
        foreach ($headers as $name => $values) {
            echo '<br/>'. $name . ": " . implode("<br/> ", $values);
        }
        $response->getBody()->write($request->getUri() . date('Y-m-d H:i:s'));
        $response->getBody()->write('<br/>' . $request->getUri()->getPath());
        $this->logger->addInfo("Say time");
        return $response;
    })->add(function ($request, $response, $next) {
        $response->getBody()->write('<p>Page Head</p>');
        $response = $next($request, $response);
        $response->getBody()->write(
                '<div style="width:60%;text-align:right;">'
                . date('Y-m-d H:i:s') . '<br/>Timestamp:' . time()
                . '</div>');
        return $response;
    });
});

$app->run();
