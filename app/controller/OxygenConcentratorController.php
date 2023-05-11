<?php
class OxygenConcentratorController
{
    private $viewPath = 'private'. 
    DIRECTORY_SEPARATOR . 'oxygenConcentrator' . 
    DIRECTORY_SEPARATOR;

    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function index()
    {
        $this->view->render($this->viewPath . 'index',
        [
            'poruka' => 'UCITANO!!!',
        ]);
    }
}