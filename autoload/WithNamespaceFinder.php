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

namespace metronome\autoload;

/**
 * Class finder strategy for version with namespaces
 *
 * @author Cédric Leclinche
 */
class WithNamespaceFinder implements ClassFinder
{
    public function find($filename)
    {
        $classes = array();
        $tokens = token_get_all(file_get_contents($filename, false));

        $classFound = false;
        $namespaceFound = false;
        $namespace = '';

        foreach($tokens as $token)
        {
            if($token[0] === T_NAMESPACE)
            {
                $namespaceFound = true;
            }
            
            if($namespaceFound && $token[0] === T_STRING)
            {
                $namespace .= $token[1];
            }
            
            if($namespaceFound && $token[0] === T_NS_SEPARATOR)
            {
                $namespace .= '\\';
            }
            
            if($namespaceFound && $token === ';')
            {
                $namespace .= '\\';
                $namespaceFound = false;
            }
            
            if($token[0] === T_CLASS || $token[0] === T_INTERFACE)
            {
                $classFound = true;
            }

            if($classFound && $token[0] === T_STRING)
            {
                $classes[$namespace.$token[1]] = $filename;
                $classFound = false;
            }
        }
        
        return $classes;
    }
}
