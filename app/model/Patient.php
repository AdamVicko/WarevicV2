<?php declare(strict_types=1);

class Patient
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

    public static function allPatient(string $condition='')
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
        $expression->bindValue('condition',$condition);
        $expression->execute();
        return $expression->fetchColumn();
    }

    public static function createPerson(array $data)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        INSERT INTO person (nameAndSurname,phone)
        VALUES(:nameAndSurname,:phone);

        ');
        $expression->bindValue(':nameAndSurname', $data['nameAndSurname'], PDO::PARAM_STR);
        $expression->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $expression->execute();
    
        return $connection->lastInsertId();
    }

    public static function updatePerson(array $data)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
            UPDATE person SET
                nameAndSurname = :nameAndSurname,
                phone = :phone
            WHERE id = :id
        ');

        $expression->bindValue(':nameAndSurname', $data['nameAndSurname'], PDO::PARAM_STR);
        $expression->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $expression->bindValue(':id', $data['id'], PDO::PARAM_INT);

        $expression->execute();
    }

    public static function createPatient(array $data)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
        
        INSERT INTO patient (person,birthDate,address,oib,patientComment)
        VALUES(:person,:birthDate,:address,:oib,:patientComment);

        ');
        $expression->bindValue(':person', $data['person'], PDO::PARAM_INT);
        $expression->bindValue(':birthDate', $data['birthDate'], PDO::PARAM_STR);
        $expression->bindValue(':address', $data['address'], PDO::PARAM_STR);
        $expression->bindValue(':oib', $data['oib'], PDO::PARAM_STR);
        $expression->bindValue(':patientComment', $data['patientComment'], PDO::PARAM_STR);
        $expression->execute();
    }

    public static function updatePatient(array $data)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
            UPDATE patient SET
                birthDate = :birthDate,
                address = :address,
                oib = :oib,
                patientComment = :patientComment
            WHERE id = :id
        ');
    
        $expression->bindValue(':birthDate', $data['birthDate'], PDO::PARAM_STR);
        $expression->bindValue(':address', $data['address'], PDO::PARAM_STR);
        $expression->bindValue(':oib', $data['oib'], PDO::PARAM_STR);
        $expression->bindValue(':patientComment', $data['patientComment'], PDO::PARAM_STR);
        $expression->bindValue(':id', $data['id'], PDO::PARAM_INT);
    
        $expression->execute();
    }

    public static function delete(int $id)
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
    
    public static function readOnePatient(int $id)
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

    public static function readOnePerson(int $person)
    {
        $connection = DB::getInstance();
        $expression = $connection->prepare('
            SELECT * FROM person 
            WHERE id=:person
        ');
        $expression->execute([
            'person' => $person
        ]);
        return $expression->fetch();
    }

}