<?php

namespace app;

class Router
{
    private $getRoutes;
    private $postRoutes;

    public function get($url, $fn)
    {

        $this->getRoutes[$url] = $fn;
    }
    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;

    }
    public function call()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['PATH_INFO'] ?? '/';
        // print_r($method);
        // print_r($path);
        $fn = null;

        if ($method === 'GET') {
            if (isset($this->getRoutes[$path])) {
                $fn = $this->getRoutes[$path];
            }

        } else {
            if (isset($this->postRoutes[$path])) {
                $fn = $this->postRoutes[$path];
            }

        }
        if (!($fn)) {
            echo 'Route not found int the api';
            return;
        }
        call_user_func($fn);
    }
}