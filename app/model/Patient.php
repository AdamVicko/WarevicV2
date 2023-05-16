<?php

class Patient
{
    public static function read($condition='',$page=1)
    {

        $condition = '%' . $condition . '%';
        $nrpp = App::config('nrpp');
        $start = ($page * $nrpp) - $nrpp;

        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT 	
            a.id,
            a.birthDate,
            a.address,
            a.oib,
            a.patientComment,
            d.nameAndSurname,
            d.phone,
            e.serialNumber,
            b.deliveryDate,
            b.active AS delivered
        FROM patient a
            LEFT JOIN delivery b ON a.id = b.patient
            LEFT JOIN collection c ON b.collection = c.delivery
            LEFT JOIN person d ON a.person = d.id
            LEFT JOIN oxygenconcentrator e ON e.id = b.oxygenConcentrator
        WHERE d.nameAndSurname LIKE :condition
        GROUP BY 
            a.id,
            a.person,
            a.birthDate,
            a.address,
            a.oib,
            a.patientComment,
            d.nameAndSurname,
            d.phone,
            b.deliveryDate,
            b.active
        ORDER BY b.deliveryDate ASC LIMIT :start, :nrpp;

        ');
        $expression->bindValue('start',$start, PDO::PARAM_INT); 
        $expression->bindValue('nrpp',$nrpp, PDO::PARAM_INT); 
        $expression->bindParam('condition', $condition);

        $expression->execute();
        return $expression->fetchAll();
    }

    public static function allPatient($condition='')
    {
        $condition = '%' . $condition . '%';
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT COUNT(*)
        FROM 
            patient a
        LEFT JOIN person b ON a.person = b.id
        WHERE b.nameAndSurname LIKE :condition;
        
        ');
        $expression->execute([
            'condition'=>$condition
        ]);
        return $expression->fetchColumn();
    }

    public static function create($parameters)
    {

    }

    public static function update($parameters)
    {

    }

    public static function delete($id)
    {
        $connection = DB::getInstance();

        $expression = $connection->prepare('
        
        DELETE FROM delivery
        WHERE patient=:id
    
        ');
        $expression->execute([
            'id'=>$id
        ]);

        $expression = $connection->prepare('
        
        DELETE FROM patient
        WHERE id=:id
    
        ');
        $expression->execute([
            'id'=>$id
        ]);

    }

    public static function firstPatient()
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT id FROM patient
        ORDER BY id LIMIT 1;

        ');
        $expression->execute();
        $id=$expression->fetchColumn();
        return (int)$id;
    }
    
    public static function readOne($id)
    {
        
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
            SELECT * FROM patient
            WHERE id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        return $expression->fetch();
    }

}