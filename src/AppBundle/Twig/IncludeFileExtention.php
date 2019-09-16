<?php

namespace AppBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

class IncludeFileExtention extends Twig_Extension
{
    public function __construct($web_dir)
    {
        $this->web_dir = $web_dir;
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('fileGetContents', array($this, 'fileGetContents') ),
        );
    }

    public function fileGetContents($file)
    {
        $file = '/' . ltrim($file, '/');
        if (!file_exists($this->web_dir . $file)) {
            return;
        }
        
        return file_get_contents($this->web_dir . $file);
    }

    public function getName()
    {
        return 'include_file_extension';
    }
}