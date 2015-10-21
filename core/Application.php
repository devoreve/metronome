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

namespace metronome\core;
use metronome\container\Container;
use metronome\router\Router;
use metronome\router\RouterException;
use metronome\http\Request;

/**
 * Class for managing the whole application
 *
 * @author Cédric Leclinche
 */
class Application extends Container
{
    private $missing;
    private $paths;
    
    public function __construct()
    {
        parent::__construct();
        $this->paths = array();
    }
    
    /**
     * Function called if no route matche
     * 
     * @param Callable $missing
     */
    public function missing(Callable $missing)
    {
        $this->missing = $missing;
    }

    /**
     * Run application, depending on the user request and the different routes
     * 
     * @param Request $request User request
     * @param Router $router Router which contains the whole routes
     */
    public function run(Request $request, Router $router)
    {
        try
        {
            $response = $router->run($request->uri(), $request->method());
            $response->send();
        }
        catch(RouterException $re)
        {
            return call_user_func($this->missing);
        }
    }
    
    public function setPath($key, $path)
    {
        $this->paths[$key] = $path;
        return $this;
    }
    
    public function getPath($key)
    {
        return $this->paths[$key];
    }
}
