<?php

class ClassLoader
{

    protected $directories = array();

    public function register()
    {
        sql_autoload_register(array($this, 'loadClass'));
    }

    public function registerDirectory($directory)
    {
        $this->directories[] = $directory;
    }

    public function loadClass($className)
    {
        foreach ($this->directories as $directory) {
            $file = $directory . '/' .$className . '.php';
            if (is_readable($file)) {
                require $file; // includeだと、指定したファイルが読み込めない場合warningのみ出して後続の処理を続行させてしまう
                return;
            }
        }
    }
}
