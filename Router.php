<?php

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
}