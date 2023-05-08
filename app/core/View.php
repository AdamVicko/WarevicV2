<?php

class View{

    private $template;

    public function __construct($template='template')
    {
        $this->template = basename($template);
    }

    public function render($name,$args=[])
    {
        ob_start();
        extract($args);
        include BP . 'app/view/'.$name.'.phtml';
        $content = ob_get_clean();
        if($this->template){
            include BP . 'app/view/'.$this->template.'.phtml';
        }else{
            echo $content;
        }
        return $this;
    }
}