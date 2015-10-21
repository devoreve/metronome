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
 * Class for managing user actions
 *
 * @author Cédric Leclinche
 */
abstract class Controller
{
    /**
     * Create a response with the content of the view specified
     * 
     * @param string $view View name
     * @param array $vars Vars in the view
     * @return \metronome\http\Response
     * @throws \Exception
     */
    protected function render($view, Array $vars = null)
    {
        $app = App::instance();
        $pathView = explode('.', $view);
        $file = $app->getPath('views').$pathView[0].'/'.$pathView[1].'.php';
        
        if(!file_exists($file))
            throw new \Exception('View does not exist');
        
        ob_start();
        if(!empty($vars)) extract($vars);
        require $file;
        $content = ob_get_clean();
        
        require $app->getPath('views').'layout.php';
        
        $contents = ob_get_contents();
        ob_end_clean();
        
        $response = new \metronome\http\Response();
        $response->setContent($contents);
        return $response;
    }
}
