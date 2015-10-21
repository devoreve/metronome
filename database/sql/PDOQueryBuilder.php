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

namespace metronome\database\sql;
use metronome\database\formater\DbDataResult;

/**
 * QueryBuilder for pdo
 *
 * @author Cédric Leclinche
 */
class PDOQueryBuilder extends DatabaseQueryBuilder
{
    /**
     * Get data of a sql select request
     * 
     * @return array Data retrieved
     */
    public function get()
    {
        $conditions = array();
        $data = array();

        foreach($this->conditions as $cond)
        {
            if(substr_count($cond, '='))
                $operator = '=';
            else if(substr_count($cond, '<'))
                $operator = '<';
            else if(substr_count($cond, '>'))
                $operator = '<';
            else if(substr_count($cond, '<>'))
                $operator = '<>';

            $tmp = explode('=', $cond);
            $field = trim($tmp[0]);
            $value = trim($tmp[1], ' \'');
            $val = ctype_digit($value) ? intval($value) : $value;
            $key = ':' . $field;
            $conditions[] = $field . $operator . $key;
            $data[$key] = $val;
            
        }

        $this->conditions = $conditions;
        $sql = parent::get();
        $result = $this->db->execute($sql, $data);
        $result->setFormater(DbDataResult::instance('pdo', $this->format['type'], $result->getStatement(), $this->format['class']));
        
        return $result->getResult($sql);
    }
}
