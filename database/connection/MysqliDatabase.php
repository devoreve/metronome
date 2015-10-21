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

namespace metronome\database\connection;
use metronome\database\result\DbResult;

/**
 * Manage Mysqli database
 *
 * @author Cédric Leclinche
 */
class MysqliDatabase implements Database
{
    private $mysqli;
    
    public function __construct(Array $params)
    {
        $this->mysqli = new \mysqli($params['host'], $params['username'], $params['password'], $params['dbname']);
    }
    
    /**
     * Execute prepared query with mysqli
     * 
     * @param string $query Prepared query
     * @param array $params Args for the prepared query
     * @return DatabaseResult
     */
    public function execute($query, $params = null)
    {
        $statement = $this->mysqli->prepare($query);
        $args = array();
        $args[0] = '';
        
        if(!empty($params))
        {
            foreach($params as $key => $param)
            {
                if(is_string($param))
                    $args[0] .= 's';
                else if(is_int($param))
                    $args[0] .= 'i';
                else if(is_double($param))
                    $args[0] .= 'd';
                else
                    $args[0] .= 'b';
                
                $args[] = &$params[$key];
            }
            
            call_user_func_array(array($statement, 'bind_param'), $args);
        }
     
        $statement->execute();
        return DbResult::instance('mysqli', $statement);
    }
    
    function getMysqli()
    {
        return $this->mysqli;
    }

    function setMysqli($mysqli)
    {
        $this->mysqli = $mysqli;
    }
}
