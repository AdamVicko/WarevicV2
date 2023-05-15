<?php

class OxygenConcentrator
{
    public static function read($condition='',$page=1)
    {

        $condition = '%' . $condition . '%';
        $nrpp = App::config('nrpp');
        $start = ($page * $nrpp) - $nrpp;

        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT a.id,
                a.serialNumber,
                a.workingHour,
                a.manufacturer,
                a.model,
                a.oxygenConcentratorComment,
                a.buyingDate,
                b.active AS delivered
        FROM oxygenConcentrator a
            LEFT JOIN delivery b ON a.id = b.oxygenConcentrator
            LEFT JOIN collection c ON b.collection = c.delivery
        WHERE a.serialNumber
        LIKE :condition
        GROUP BY a.id,
                a.serialNumber,
                a.workingHour,
                a.manufacturer,
                a.model,
                a.oxygenConcentratorComment,
                a.buyingDate,
                b.active 
        ORDER BY a.buyingDate ASC LIMIT :start, :nrpp;

        ');
        $expression->bindValue('start',$start, PDO::PARAM_INT); 
        $expression->bindValue('nrpp',$nrpp, PDO::PARAM_INT); 
        $expression->bindParam('condition', $condition);

        $expression->execute();
        return $expression->fetchAll();
    }

    public static function allOxygen($condition='')
    {
        $condition = '%' . $condition . '%';
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT COUNT(*)
        FROM 
            oxygenConcentrator
        WHERE serialNumber
        LIKE :condition;
        
        ');
        $expression->execute([
            'condition'=>$condition
        ]);
        return $expression->fetchColumn();
    }

    public static function create($parameters)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        INSERT INTO oxygenConcentrator (serialNumber,workingHour,manufacturer,model,oxygenConcentratorComment,buyingDate)
        VALUES(:serialNumber,:workingHour,:manufacturer,:model,:oxygenConcentratorComment,:buyingDate);

        ');
        $expression->execute($parameters);
    }

    public static function update($parameters)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        UPDATE oxygenConcentrator  SET
            serialNumber=:serialNumber,
            workingHour=:workingHour,
            manufacturer=:manufacturer,
            model=:model,
            oxygenConcentratorComment=:oxygenConcentratorComment,
            buyingDate=:buyingDate 
        WHERE id=:id


        ');
        $expression->execute($parameters);
    }

    public static function delete($id)
    {
        $connection = DB::getInstance();

        $expression = $connection->prepare('
        
        DELETE FROM delivery
        WHERE oxygenConcentrator=:id
    
        ');
        $expression->execute([
            'id'=>$id
        ]);

        $expression = $connection->prepare('
        
        DELETE FROM oxygenConcentrator
        WHERE id=:id
    
        ');
        $expression->execute([
            'id'=>$id
        ]);

    }

    public static function firstOxygenConcentrator()
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT id FROM oxygenConcentrator
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
        
            SELECT * FROM oxygenConcentrator
            WHERE id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        return $expression->fetch();
    }

}