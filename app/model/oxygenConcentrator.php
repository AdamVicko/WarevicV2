<?php

class OxygenConcentrator
{
    public static function read($uvjet='',$stranica=1)
    {

        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;

        $veza = DB::getInstance(); //read napravljen da nemogu brisati OC ako nije prikupljen
        $izraz = $veza->prepare('
        
        SELECT a.sifra,
                a.serialNumber,
                a.workingHour,
                a.manufacturer,
                a.model,
                a.oxygenConcentratorComment,
                a.buyingDate,
                c.active AS delivered
        FROM oxygenConcentrator a
            LEFT JOIN collection b ON a.sifra = b.oxygenConcentrator
            LEFT JOIN delivery c ON a.sifra = c.oxygenConcentrator
        WHERE a.serialNumber
        LIKE :uvjet
        GROUP BY a.sifra,
                a.serialNumber,
                a.workingHour,
                a.manufacturer,
                a.model,
                a.oxygenConcentratorComment,
                a.buyingDate,
                c.active 
        ORDER BY a.buyingDate ASC LIMIT :pocetak, :brps;

        ');
        $izraz->bindValue('pocetak',$pocetak, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindValue('brps',$brps, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindParam('uvjet', $uvjet);

        $izraz->execute();
        return $izraz->fetchAll();
    }
}