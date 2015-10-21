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

class AutoloaderException extends \Exception
{
    
}

class ExtensionFilterIterator extends \FilterIterator
{
    private $ext;
    
    public function accept()
    {
        return substr($this->current(), -1 * strlen($this->ext)) === $this->ext;
    }
    
    public function setExtension($ext)
    {
        $this->ext = $ext;
    }
}

/**
 * Load classes
 *
 * @author Cédric Leclinche
 */
class Autoloader
{
    private static $instance = null;
    private $directories;
    private $classes;
    private $loadClasses;
    private $cache;
    private $classFinder;
    
    public static function instance($cache)
    {
        if(self::$instance === null)
        {
            self::$instance = new self($cache);
        }
        
        return self::$instance;
    }
    
    private function __construct($cache)
    {
        $this->directories = array();
        $this->classes = array();
        $this->loadClasses = true;
        $this->cache = $cache;
        $this->classFinder = ClassFinderFactory::create();
    }
    
    /**
     * Register autoload function
     * 
     */
    public function register()
    {
        spl_autoload_register([$this, 'autoload']);
    }
    
    /**
     * Call class loader function and store classes in a cache file
     *  
     * @param string $classname Class name we want to load
     * @return boolean
     */
    public function autoload($classname)
    {
        if($this->loadClass($classname))
            return true;
        
        if($this->loadClasses)
        {
            $this->loadClasses = false;
            $this->search();
            file_put_contents($this->cache.'autoload.cache.php', '<?php $classes = '.var_export($this->classes, true).'; ?>');
            return $this->autoload($classname);
        }
        
        return false;
    }
    
    /**
     * Add a directory in which we want to search for classes to load
     * 
     * @param string $directory Name of the directory
     * @param boolean $recursive True (default) if search must be recursive
     * @return \metronome\autoload\Autoloader
     * @throws AutoloaderException
     */
    public function addDirectory($directory, $recursive = true)
    {
        if(!is_readable($directory)) 
            throw new AutoloaderException('Cannot read from directory: '.$directory);
        
        $this->directories[$directory] = $recursive;
        return $this;
    }

    /**
     * Search all classes in directories chosen and add them in an array
     * 
     * @throws AutoloaderException
     */
    private function search()
    {
        foreach($this->directories as $directory => $recursive)
        {
            if($recursive)
                $directories  = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
            else
            {
                $directories = new \DirectoryIterator($directory);
            }
            
            $files = new ExtensionFilterIterator($directories);
            $files->setExtension('.php');
            
            foreach($files as $filename)
            {
                $filename = !$recursive ? $directory.DIRECTORY_SEPARATOR.$filename : $filename;
                
                if(!is_readable($filename))
                    throw new AutoloaderException('Cannot read from file: '.$filename);
                
                $classes = $this->classFinder->find((string)$filename);
                foreach($classes as $classname => $filename)
                {
                    $this->classes[$classname] = $filename; 
                }
            }
        }
    }
    
    /**
     * Load class specified
     * 
     * @param string $classname Class name
     * @return boolean
     */
    private function loadClass($classname)
    {
        if(empty($this->classes))
        {
            $filename = $this->cache.'autoload.cache.php';
            if(is_readable($filename))
            {
                require $filename;
                $this->classes = $classes;
            }
        }
        
        if(isset($this->classes[$classname]))
        {
            require_once $this->classes[$classname];
            return true;
        }
        
        return false;
    }
    
    public function __clone(){}
}
