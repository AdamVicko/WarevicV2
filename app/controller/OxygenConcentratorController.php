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
        if('GET' === $_SERVER['REQUEST_METHOD']) {
            $this->callView(
                [
                    'e'=>$this->initialData(),
                    'message'=>$this->message
                ]
            );
        }
        $this->e = (object)$_POST; 
        if(false === $this->controlNew()) {
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

    public function update($id='')
    {
        if( 'GET' === $_SERVER['REQUEST_METHOD'] ) { 
            if( 0 === strlen(trim($id)) ) {
                header('location: ' . App::config('url') . 'logIn/logOut' );
                return;
            }
            $id=(int)$id;
            if(0 === $id) {
                header('location: ' . App::config('url') . 'logIn/logOut' );
                return;
            }
            $this->e = OxygenConcentrator::readOne($id);
            if(null === $this->e) {   
                header('location: ' . App::config('url') . 'logIn/logOut' );
                return;
            }
            $this->view->render($this->viewPath . 
            'update',[
                'e'=>$this->e,
                'message'=>'Update data of Oxygen Concentrator!'
            ]);
            return;
        }
        $this->e = (object)$_POST;
        if(false === $this->controlNew()) {
                $this->view->render($this->viewPath . 
                'update',[
                    'e'=>$this->e,
                    'message'=>$this->message
                ]);
                return;
            }
        $this->e->id=$id;
        OxygenConcentrator::update((array)$this->e);   
        $this->view->render($this->viewPath . 
        'update',[
            'e'=>$this->e,
            'message'=>'Update complete!'
        ]);
    }

    public function delete($id=0)
    {
        $id=(int)$id;
        if(0 === $id)
        {
            header('location: ' . App::config('url') . 'logIn/logOut' );
            return;
        }
        OxygenConcentrator::delete($id);
        header('location: ' . App::config('url') . 'oxygenConcentrator/index' );
    }

    private function callView($parameters)
    {
        $this->view->render($this->viewPath . 
        'new',$parameters);
    }

    private function controlNew()
    {
        return $this->controlSerialNumber() && $this->controlWorkingHour() && 
        $this->controlBuyingDate() && $this->controlModel();
    }

    private function controlSerialNumber()
    {

        $s = $this->e->serialNumber;
        if( 0 == strlen(trim($s)) )
        {
            $this->message='Serial number is mandatory!';
            return false;
        }

        if(50 < strlen(trim($s)))
        {
            $this->message='Must not have more than 50 characters in Serial number!';
            return false;
        }

        return true;
    }
    private function controlWorkingHour()
    {
        $s =$this->e->workingHour; 
        if( 0 === strlen(trim($s)) )
        {
            $this->message='Working hours are mandatory!';
            return false;
        }

        if(50 < strlen(trim($s)))
        {
            $this->message='Must not have more than 50 characters in Working hours!';
            return false;
        }
        if(0 >= $s)
        {
            $this->message='OC Working hours must be greater than zero!';
            return false;
        }
        if( 25000 < $s)
        {
            $this->message='OC Working hours must be lower then twentyfive thousand!';
            return false;
        }

        return true;
    }
    private function controlModel()
    {
        $s = $this->e->model;
        if( 0 === strlen(trim($s)) )
        {
            $this->message='Model is mandatory!';
            return false;
        }

        if( 20 < strlen(trim($s)) )
        {
            $this->message='Must not have more than 20 characters in Model!';
            return false;
        }

        return true;
    }
    private function controlBuyingDate()
    {

        $s = $this->e->buyingDate;
        if( 0 === strlen(trim($s)) )
        {
            $this->message='Date of buying is mandatory!';
            return false;
        }
        return true;
    }
}