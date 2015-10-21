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

namespace metronome\database\formater;

/**
 * Description of DbDataResult
 *
 * @author Cédric Leclinche
 */
class DbDataResult
{
    public static function instance($type, $format, $statement, $class = null)
    {
        if($format === 'array')
        {
            if($type === 'pdo')
                return new PdoArrayFormater($statement);
            else if($type === 'mysqli')
                return new MysqliArrayFormater($statement);
        }
        else if($format === 'object')
        {
            if($type === 'pdo')
                return new PdoObjectFormater($statement, $class);
            else if($type === 'mysqli')
                return new MysqliObjectFormater($statement, $class);
        }
    }
}
