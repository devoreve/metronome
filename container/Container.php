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

namespace metronome\container;

/**
 * Description of Container
 *
 * @author Cédric Leclinche
 */
class Container implements \ArrayAccess
{
    private $singletons;
    private $factories;
    private $instances;
    
    public function __construct()
    {
        $this->singletons = array();
        $this->factories = array();
        $this->instances = array();
    }
    
    /**
     * Store a key and a Callable in singletons registry
     * 
     * @param string $key Singleton name
     * @param Callable $callable Function 
     */
    public function singleton($key, Callable $callable)
    {
        $this->singletons[$key] = $callable;
    }
    
    /**
     * Store a key and a Callable in factories registry
     * 
     * @param string $key Factory name
     * @param Callable $callable Function 
     */
    public function factory($key, Callable $callable)
    {
        $this->factories[$key] = $callable;
    }
    
    /**
     * Call callable corresponding to a key in the singletons registry or
     * factories registry
     * 
     * @param string $key
     * @return instance of a class
     */
    public function get($key)
    {
        if(isset($this->factories[$key]))
            return $this->factories[$key]();
        
        if(!isset($this->instances[$key]))
            $this->instances[$key] = $this->singletons[$key]();
        
        return $this->instances[$key];
    }

    public function offsetExists($offset)
    {
        return isset($this->factories[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if(!$value instanceof \Closure)
        {
            $value = function() use ($value)
            {
                return $value;
            };
        }
        
        $this->factories[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->factories[$offset]);
    }

}
