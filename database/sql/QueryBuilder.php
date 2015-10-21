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

/**
 * Build sql queries easily
 *
 * @author Cédric Leclinche
 */
class QueryBuilder
{
	protected $fields;
	protected $tables;
	protected $conditions;
	protected $joins;

	public function __construct()
	{
		$this->init();
	}
    
    protected function init()
    {
        $this->fields = array();
		$this->tables = array();
		$this->conditions = array();
		$this->joins = array();
    }

    /**
     * Create select fields in select query
     * 
     * @return QueryBuilder
     */
	public function select()
	{
		$this->fields = func_get_args();
		return $this;
	}

    /**
     * Add a table in select query
     * 
     * @param string $table Table name
     * @return QueryBuilder
     */
	public function from($table)
	{
		$this->tables[] = $table;
		return $this;
	}

    /**
     * Add where clause
     * 
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return QueryBuilder
     */
	public function where($field, $operator, $value)
	{
		$val = is_string($value) ? '\''.$value.'\'' : $value;
		$this->conditions[] = $field.' '.$operator.' '.$val;
		return $this;
	}

    /**
     * Create join in select queries
     * 
     * @param string $table
     * @param string $value
     * @param string $join
     * @return QueryBuilder
     */
	public function join($table, $value, $join)
	{
        $this->joins[] = ' INNER JOIN '.$table.' ON '.$value.'='.$join;
		return $this;
	}

    /**
     * Create left join in select queries
     * 
     * @param string $table
     * @param string $value
     * @param string $join
     * @return QueryBuilder
     */
	public function leftJoin($table, $value, $join)
	{
		$this->joins[] = ' LEFT JOIN '.$table.' ON '.$value.'='.$join;
		return $this;
	}

    /**
     * Build query
     * 
     * @return string The query built
     */
	public function get()
	{
		$sql = 'SELECT '.implode(', ', $this->fields)." FROM ".implode(', ', $this->tables);

		if(!empty($this->joins))
			$sql .= implode(' ', $this->joins);

		if(!empty($this->conditions))
			$sql .= " WHERE ".implode(' AND ', $this->conditions);
        
        $this->init();

		return $sql;
	}

    /**
     * Get sql query to insert values in a table
     * 
     * @param string $table
     * @param array $data
     * @return string The query built
     */
	public function insert($table, Array $data)
	{
		$data = $this->format($data);
		return 'INSERT INTO '.$table.'('.implode(', ', array_keys($data)).') VALUES('.implode(', ', array_values($data)).')';
	}

    /**
     * Get sql query to update values in a table with id
     * 
     * @param string $table
     * @param array $data
     * @param int $id
     * @return string The query built
     */
	public function update($table, Array $data)
	{
        $data = $this->format($data);
		$updates = array();

		foreach($data as $field => $value)
		{
			$updates[] = $field.'='.$value;
		}
        
        $sql = 'UPDATE '.$table.' SET '.implode(', ', $updates);
        
        if(!empty($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);
        
        $this->conditions = array();
        
		return $sql;
	}

    /**
     * Get sql query to delete rows in a table
     * 
     * @param string $table
     * @return string The query built
     */
	public function delete($table)
	{
		$sql = 'DELETE FROM '.$table;
		if(!empty($this->conditions))
			$sql .= ' WHERE '.implode(' AND ', $this->conditions);

		$this->conditions = array();

		return $sql;
	}
    
    /**
     * Format data to be null for dbms
     * 
     * @param array $data
     * @return array
     */
    private function format(Array $data)
    {
        array_walk($data, function(&$item, $key)
		{
			if(empty($item))
				$item = 'NULL';
			else
				$item = !is_numeric($item) ? "'$item'" : $item;
		});
        
        return $data;
    }
}
