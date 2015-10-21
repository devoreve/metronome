<?php

/*
 * The MIT License
 *
 * Copyright 2015 Cédric Leclinche.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace metronome\router;

/**
 * Description of Router
 *
 * @author Cédric Leclinche
 */

class Router
{
    private $routes;
    private $baseURL;
    
    public function __construct($baseURL = '')
    {
        $this->routes = array();
        $this->baseURL = $baseURL;
    }
    
    /**
     * Add a route in routes registry for the method GET
     * 
     * @param string $path URL for the route
     * @param mixed $callable Function to execute or controller to call
     */
    public function get($path, $callable)
    {
        $path = trim($path, '/');
        $this->routes['GET'][] = new Route($this->baseURL.$path, $callable);
    }
    
    /**
     * Add a route in routes registry for the method POST
     * 
     * @param string $path URL for the route
     * @param mixed $callable Function to execute or controller to call
     */
    public function post($path, $callable)
    {
        $path = trim($path, '/');
        $this->routes['POST'][] = new Route($this->baseURL.$path, $callable);
    }
    
    /**
     * Run router search for route matching
     * 
     * @param string $uri URI requested by user
     * @param string $method Method of the request
     * @return mixed
     * @throws RouterException
     */
    public function run($uri, $method)
    {
        foreach($this->routes[$method] as $route)
        {
            if($route->match($uri))
            {
                return $route->call();
            }
        }
        
        throw new RouterException('No route matched');
    }
}
