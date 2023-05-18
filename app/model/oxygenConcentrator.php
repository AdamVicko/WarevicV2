<?php declare(strict_types=1);

class OxygenConcentrator
{
    public static function read(string $condition='', int $page=1)
    {

        $condition = '%' . $condition . '%';
        $nrpp = App::config('nrpp');
        $start = ($page * $nrpp) - $nrpp;

        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        SELECT 
            a.id,
            a.serialNumber,
            a.workingHour,
            a.manufacturer,
            a.model,
            a.oxygenConcentratorComment,
            a.buyingDate,
            e.nameAndSurname,
            b.active AS delivered
        FROM oxygenConcentrator a
            LEFT JOIN delivery b ON a.id = b.oxygenConcentrator
            LEFT JOIN collection c ON b.collection = c.delivery
            LEFT JOIN patient d ON b.patient = d.id
            LEFT JOIN person e ON d.person = e.id
        WHERE a.serialNumber LIKE :condition
        GROUP BY 
            a.id,
            a.serialNumber,
            a.workingHour,
            a.manufacturer,
            a.model,
            a.oxygenConcentratorComment,
            a.buyingDate,
            e.nameAndSurname,
            b.active 
        ORDER BY b.deliveryDate ASC LIMIT :start, :nrpp;

        ');
        $expression->bindValue('start',$start, PDO::PARAM_INT); 
        $expression->bindValue('nrpp',$nrpp, PDO::PARAM_INT); 
        $expression->bindParam('condition', $condition);

        $expression->execute();
        return $expression->fetchAll();
    }

    public static function allOxygen(string $condition='')
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
        $expression->bindValue('condition',$condition);
        $expression->execute();
        return $expression->fetchColumn();
    }

    public static function create()
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        INSERT INTO oxygenConcentrator (serialNumber,workingHour,manufacturer,model,oxygenConcentratorComment,buyingDate)
        VALUES(:serialNumber,:workingHour,:manufacturer,:model,:oxygenConcentratorComment,:buyingDate);

        ');
        $expression->bindValue('serialNumber',$_POST['serialNumber']);
        $expression->bindValue('workingHour',$_POST['workingHour']);
        $expression->bindValue('manufacturer',$_POST['manufacturer']);
        $expression->bindValue('model',$_POST['model']);
        $expression->bindValue('oxygenConcentratorComment',$_POST['oxygenConcentratorComment']);
        $expression->bindValue('buyingDate',$_POST['buyingDate']);
        $expression->execute();
    }

    public static function update(int $id)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        UPDATE oxygenConcentrator SET
            serialNumber=:serialNumber,
            workingHour=:workingHour,
            manufacturer=:manufacturer,
            model=:model,
            oxygenConcentratorComment=:oxygenConcentratorComment,
            buyingDate=:buyingDate 
        WHERE id=:id
    
    
        ');
        $expression->bindValue('serialNumber', $_POST['serialNumber']);
        $expression->bindValue('workingHour', $_POST['workingHour']);
        $expression->bindValue('manufacturer', $_POST['manufacturer']);
        $expression->bindValue('model', $_POST['model']);
        $expression->bindValue('oxygenConcentratorComment', $_POST['oxygenConcentratorComment']);
        $expression->bindValue('buyingDate', $_POST['buyingDate']);
        $expression->bindValue('id', $id);
        $expression->execute();
    }

    public static function delete(int $id)
    {
        $connection = DB::getInstance();

        $deleteDelivery  = $connection->prepare('
        
        DELETE FROM delivery
        WHERE oxygenConcentrator=:id
    
        ');
        $deleteDelivery->execute([
            'id' => $id
        ]);

        $deleteConcentrator = $connection->prepare('
        
        DELETE FROM oxygenConcentrator
        WHERE id=:id
    
        ');
        $deleteConcentrator->execute([
            'id' => $id
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
    
    public static function readOne(int $id)
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