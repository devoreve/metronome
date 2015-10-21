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
 * Description of Route
 *
 * @author Cédric Leclinche
 */
class Route
{
    private $path;
    private $callable;
    private $params;
    
    public function __construct($path, $callable)
    {
        $this->path = $path;
        if(is_string($callable))
        {
            $args = explode('::', $callable);
            $controllername = '\\app\\controllers\\'.$args[0];
            $controller = new $controllername();
            
            $this->callable = array($controller, $args[1]);
        }
        else
            $this->callable = $callable;
    }
    
    /**
     * Check if a route matches with uri given
     * 
     * @param string $uri
     * @return boolean
     */
    public function match($uri)
    {
        $path = preg_replace('#/\{[\w]+\}#', '/([\w]+)', $this->path);
        $regex = '#^'.$path.'$#';
        
        if(!preg_match($regex, $uri, $matches))
        {
            return false;
        }
        
        array_shift($matches);
        $this->params = $matches;
        
        return true;
    }
    
    /**
     * Call function of the route
     * 
     */
    public function call()
    {
        return call_user_func_array($this->callable, $this->params);
    }
}
