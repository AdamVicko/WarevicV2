<?php

class Worker
{
    public static function authorize($email,$password)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        SELECT * FROM worker WHERE email=:email
        
        ');
        $izraz->execute([
            'email'=>$email
        ]);

        $worker = $izraz->fetch();

        if(null === $worker) {
            return null;
        }

        if(false === password_verify($password,$worker->password)) {
            return null;
        }

        unset($worker->password);
        
        return $worker;
    }
}