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

namespace metronome\cache;

/**
 * Description of Cache
 *
 * @author Cédric Leclinche
 */
class Cache
{
	protected $dir;

	public function __construct($dir)
	{
		$this->dir = $dir;
		
		if(!is_dir($dir))
			mkdir($dir);
	}

    /**
     * Read content of a file
     * 
     * @param string $filename Filename we want to read
     * @return string|boolean
     */
	public function read($filename)
	{
		$file = $this->dir.$filename;
		if(file_exists($file) && is_file($file))
			return file_get_contents($file);
		else
			return false;
	}

    /**
     * Write content in a file
     * 
     * @param string $filename Filename in which we want to write
     * @param string $content Content we want to write in the file
     * @return boolean
     */
	public function write($filename, $content)
	{
		return file_put_contents($this->dir.$filename, $content);
	}

    /**
     * Delete all files in cache file
     * 
     */
	public function clean()
	{
		$files = glob($this->dir.'*');

		foreach($files as $file)
		{
			if(is_file($file))
				unlink($file);
		}
	}
}