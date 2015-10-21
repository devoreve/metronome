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

/**
 * Class for managing data
 *
 * @author Cédric Leclinche
 */
abstract class Model implements Repository
{
    protected $table;
    protected $entity;
    protected $qb;
    
    public function __construct($qb)
    {
        $this->qb = $qb;
        $qb->format(array('type' => 'object', 'class' => $this->entity));
    }
    
    /**
     * Get all informations of the table
     * 
     * @return array
     */
    public function all()
    {
        return $this->qb->select('*')
                        ->from($this->table)
                        ->get();
    }
    
    /**
     * Get information of an entity corresponding to the id specified
     * 
     * @param int $id
     * @return array
     */
    public function find($id)
    {
        $users = $this->qb->select('*')
                        ->from($this->table)
                        ->where('id', '=', $id)
                        ->get();
        
        return $users[0];
    }
    
    /**
     * Save entity in database
     * 
     * @param mixed $entity
     * @return type
     */
    public function save($entity)
    {
        $reflection = new \ReflectionClass($entity);
        $properties = $reflection->getProperties();
        $data = array();
        
        foreach($properties as $p)
        {
            $p->setAccessible(true);
            $name = $p->getName();
            $value = $p->getValue($entity);
            if($name === 'id')
                $id = $value;
            else
                $data[$name] = $value;
        }
        
        return empty($id) ? $this->qb->insert($this->table, $data) : 
                            $this->qb->where('id', '=', $id)->update($this->table, $data);
    }
}
