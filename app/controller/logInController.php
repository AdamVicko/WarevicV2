<?php

class logInController 
{
    protected $view;

    public function logIn()
    {
        $view=new View();
        $view->render('logIn',[
            'poruka'=>'',
            'email'=>''
        ]);
    }

    public function authorization()
    {
        if(false === isset($_POST['email']) || strlen(trim($_POST['email'])) === 0) {
            $this->view->render('logIn',
            [
                'poruka'=>'Enter email!',
                'email'=>''
            ]);
            return;
        }

        if(false === isset($_POST['password']) || strlen(trim($_POST['password']))===0) {
            $this->view->render('logIn',
            [
                'poruka'=>'Enter correct password!',
                'email'=>$_POST['email']
            ]);
            return;
        }

        $worker = Worker::autoriziraj($_POST['email'], $_POST['password']);

        if($worker==null)
        {
            $this->view->render('logIn',[
                'poruka' =>'Wrong email or password!',
                'email'=> $_POST['email']
            ]);
            return;
        }

        //uspjesno prijavljen
        $_SESSION['auth']=$worker;
        header('location:' . App::config('url') . 
        'wanted/page');
        

    }
}

?>