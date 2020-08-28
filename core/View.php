<?php

class View
{

    protected $baseDir;
    protected $defaults;
    protected $layoutVariables = array();

    public function __construct($baseDir, $defaults = array())
    {
        $this->baseDir = $baseDir;
        $this->defaults = $defaults;
    }
    
    public function setLayoutVar($name, $value)
    {
        $this->layoutVariables[$name] = $value;
    }

    public function render($_path, $_valiables = array(), $_layout = false)
    {
        $_file = $this->baseDir . '/' . $_path . '.php';

        extract(array_merge($this->defaults, $_variables));

        ob_start();
        ob_implicit_flush(0);

        require $_file;

        $content = ob_get_clean();

        if ($_layout) {
            $content = $this->render($_layout,
            array_merge($this->layoutVariables, array(
                '_content' => $content
               )
            ));
        }

        return $content;
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
