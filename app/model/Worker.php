<?php

class Worker
{
    public static function authorize($email,$password)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from worker where email=:email
        
        ');
        $izraz->execute([
            'email'=>$email
        ]);

        $worker = $izraz->fetch();

        if($worker==null)
        {
            return null;
        }

        if(!password_verify($password,$worker->password))
        {
            return null;
        }

        unset($worker->password);
        
        return $worker;
    }
}