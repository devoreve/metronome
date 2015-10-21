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
 * Manage PDO database
 *
 * @author Cédric Leclinche
 */
class PDODatabase implements Database
{
    private $pdo;
    
    public function __construct(Array $params)
    {
        $dsn = $params['dbms'].':host='.$params['host'].';dbname='.$params['dbname'];
        $this->pdo = new \PDO($dsn, $params['username'], $params['password']);
    }
    
    /**
     * Execute prepared query with pdo
     * 
     * @param string $query Prepared query
     * @param array $params Args for the prepared query
     * @return DatabaseResult
     */
    public function execute($query, $params = null)
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return DbResult::instance('pdo', $statement);
    }
    
    function getPdo()
    {
        return $this->pdo;
    }

    function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }
}
