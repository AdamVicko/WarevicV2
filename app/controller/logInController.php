<?php declare(strict_types=1);

class LogInController 
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function logIn()
    {
        $this->view->render('logIn',[
            'message'=>'',
            'email'=>''
        ]);
    }

    public function logOut()
    {
        unset($_SESSION['auth']);
        session_destroy();
        header('location:' . App::config('url'));
    }

    public function authorization()
    {
        if( false === isset($_POST['email']) || 0 === strlen(trim($_POST['email'])) ) {
            $this->view->render('logIn',
            [
                'message'=>'Enter correct email!',
                'email'=>''
            ]);
            return;
        }

        if(false === isset($_POST['password']) || 0 === strlen(trim($_POST['password'])) ) {
            $this->view->render('logIn',
            [
                'message'=>'Enter correct password!',
                'email'=>$_POST['email']
            ]);
            return;
        }

        $worker = Worker::authorize($_POST['email'], $_POST['password']);

        if($worker==null) {
            $this->view->render('logIn',[
                'message' =>'Wrong email or password!',
                'email'=> $_POST['email']
            ]);
            return;
        }

        $_SESSION['auth']=$worker;
        header('location:' . App::config('url') . 
        'oxygenConcentrator/index');
    }
}
?>