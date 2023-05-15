<?php
class OxygenConcentratorController
{
    private $viewPath = 'private'. 
    DIRECTORY_SEPARATOR . 'oxygenConcentrator' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $message='';

    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    private function initialData()
    {
        $e = new stdClass();
        $e->id='';
        $e->serialNumber='';
        $e->workingHour='';
        $e->manufacturer='';
        $e->model='';
        $e->oxygenConcentratorComment='';
        $e->buyingDate='';
        return $e;
    }

    public function index()
    {
        $message='';
        if(isset($_GET['p'])) {
            switch ((int)$_GET['p']) {
                case 2:
                    $message=' To add delivery first you need to create Oxygen Concentrator!';
                    break;
                
                default:
                    $message='';
                    break;
            }
        }
        if(isset($_GET['condition'])) {
            $condition = trim($_GET['condition']);
        }else {
            $condition='';
        }
        if(isset($_GET['page'])) {
            $page = (int)$_GET['page'];
            if($page < 1) { 
                $page =1;
            }
        }else {
            $page=1;
        }
        $all = OxygenConcentrator::allOxygen($condition);
        $last = (int)ceil($all/App::config('nrpp')); 

        $this->view->render($this->viewPath . 'index',
        [
            'message' => $message,
            'data' => OxygenConcentrator::read($condition,$page),
            'css' => 'oxygenConcentrator.css',
            'page' => $page,
            'condition' => $condition,
            'last' => $last

        ]);
    }

    public function new()
    {
        if('GET' === $_SERVER['REQUEST_METHOD'])
        {
            $this->callView(
                [
                    'e'=>$this->initialData(),
                    'message'=>$this->message
                ]
            );
        }
        $this->e = (object)$_POST; 
        if(false === $this->controlNew())
            {
                $this->callView(
                    [
                        'e'=>$this->e, 
                        'message'=>$this->message
                    ]
                );
                return;
            }

            OxygenConcentrator::create((array)$this->e);
            $this->callView(
                [
                    'e'=>$this->initialData(),
                    'message'=>'Oxygen Concentrator added successfully!'
                ]
            );
    }

    private function callView($parameters)
    {
        $this->view->render($this->viewPath . 
        'new',$parameters);
    }
}