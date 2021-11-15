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
        $this->getRoutes[$url] = $fn;

    }
    public function call()
    {
        $method = $_SERVER['REAQUEST_METHOD'];
        $path = $_SERVER['PATH_INFO'] ?? '/';
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
        if (!isset($fn)) {
            echo 'Route not found int the api';
        }
        call_user_func($fn);
    }
}