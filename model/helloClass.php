<?php

class test {

    public function home() {
        return 'Go to test-home!!';
    }

}

class HomeController {

    protected $container;

    // constructor receives container instance
    public function __construct($c) {
        $this->container = $c;
    }

    public function home($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');
        var_dump($this->container);   
         $this->container->get("logger")->addInfo("Say hello in HomeController::home");     
        $response->getBody()->write('Hi, ' . $this->container->get("logger")->showMsg . '<br/>');
        return $response;
    }

    public function contact($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');
        return $response;
    }

}
