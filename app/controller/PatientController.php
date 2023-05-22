<?php declare(strict_types=1);

class PatientController
{
    private $viewPath = 'private'. 
    DIRECTORY_SEPARATOR . 'patient' . 
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
        $e->nameAndSurname='';
        $e->phone='';
        $e->birthDate='';
        $e->address='';
        $e->oib='';
        $e->patientComment='';
        return $e;
    }

    public function index()
    {
        $message='';
        if(isset($_GET['p'])) {
            switch ((int)$_GET['p']) {
                case 2:
                    $message=' To add delivery first you need to create Patient!';
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
            if(1 > $page) { 
                $page = 1;
            }
        }else {
            $page=1;
        }
        $all = Patient::allPatient($condition);
        $last = (int)ceil($all/App::config('nrpp')); 
        $last = (0 === $last) ? 1 : $last;
        $this->view->render($this->viewPath . 'index',
        [
            'message' => $message,
            'data' => Patient::read($condition,$page),
            'css' => 'patient.css',
            'page' => $page,
            'condition' => $condition,
            'last' => $last

        ]);
    }

    public function new()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            $this->callView([
                'e' => $this->initialData(),
                'message' => $this->message
            ]);
            return;
        }
        $this->e = (object) $_POST;

        if (false === ($this->controlNew())) {
            $this->callView([
                'e' => $this->e,
                'message' => $this->message
            ]);
            return;
        }
        $personId = Patient::createPerson((array) $this->e);
        $this->e->person = $personId;
        Patient::createPatient((array) $this->e);

        $this->callView([
            'e' => $this->initialData(),
            'message' => 'Patient added successfully!'
        ]);
    }

    public function update(string $id = '')
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            if (0 === strlen(trim($id))) {
                header('location: ' . App::config('url') . 'logIn/logOut');
                return;
            }
            $id = (int)$id;
            if (0 === $id) {
                header('location: ' . App::config('url') . 'logIn/logOut');
                return;
            }
            $this->e = Patient::readOnePatient($id);
            $person = Patient::readOnePerson($this->e->person);
            if (null === $this->e || null === $person) {
                header('location: ' . App::config('url') . 'logIn/logOut');
                return;
            }
            $this->view->render($this->viewPath . 'update', [
                'e' => $this->e,
                'person' => $person,
                'message' => 'Update data of Patient!'
            ]);
            return;
        } else {
            $this->e = (object)$_POST;
            if (false === $this->controlNew()) {
                $this->view->render($this->viewPath . 'update', [
                    'e' => $this->e,
                    'message' => $this->message
                ]);
                return;
            } else {
                $this->e->id = $id;
                $id = (int)$id;
                $currentPatient = Patient::readOnePatient($id);
                $personId = $currentPatient->person;

                $personUpdated = Patient::updatePerson([
                    'id' => $personId,
                    'nameAndSurname' => $this->e->nameAndSurname,
                    'phone' => $this->e->phone
                ]);

                if ($personUpdated) {
                    Patient::updatePatient([
                        'id' => $this->e->id,
                        'person' => $personId,
                        'birthDate' => $this->e->birthDate,
                        'address' => $this->e->address,
                        'oib' => $this->e->oib,
                        'patientComment' => $this->e->patientComment
                    ]);
                    $updatedPatient = Patient::readOnePatient($id);
                    $updatedPerson = Patient::readOnePerson($personId);
                    $this->view->render($this->viewPath . 'update', [
                        'e' => $updatedPatient,
                        'person' => $updatedPerson,
                        'message' => 'Update data of Patient!'
                    ]);
                    return;
                }
            }
        }    
        $this->message = 'Failed to update person information.';
        $person = Patient::readOnePerson($this->e->person);
        $this->view->render($this->viewPath . 'update', [
            'e' => $this->e,
            'person' => $person,
            'message' => $this->message
        ]);
    }

    public function delete(int $id=0)
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

    private function callView(array $parameters)
    {
        $this->view->render($this->viewPath . 
        'new',$parameters);
    }

    private function controlNew()
    {
        return $this->contorlNameAndSurname() && $this->controlOib() && 
        $this->controlPhone();
    }

    private function contorlNameAndSurname()
    {

        $s = $this->e->nameAndSurname;
        if( 0 == strlen(trim($s)) )
        {
            $this->message='Name and Surname are mandatory!';
            return false;
        }

        if(255 < strlen(trim($s)))
        {
            $this->message='Must not have more than 255 characters in Name and Surname!';
            return false;
        }

        return true;
    }

    private function controlPhone()
    {

        $s = $this->e->phone;
        if(0 === strlen(trim($s)))
        {
            $this->message='Patient telephone is mandatory!';
            return false;
        }

        if( 255 < strlen(trim($s)))
        {
            $this->message='Must not have more than 255 characters in Patient telephone!';
            return false;
        }

        return true;
    }

    private function controlOib()
    {
        $oib=$this->e->oib;
        if (false === (11 === strlen($oib)) || false === (is_numeric($oib))) {
            $this->message = 'OIB needs to have 11 numbers!';
            return false;
        }
        
        return true;
        $a = 10;
    
        for ($i = 0; $i < 10; $i++) {
    
            $a += (int)$oib[$i];
            $a %= 10;
    
            if ( $a == 0 ) { $a = 10; }
    
            $a *= 2;
            $a %= 11;
    
        }
    
        $controlNumber = 11 - $a;
    
        if (10 === $controlNumber) {$controlNumber = 0; }
    
        if(false === ($controlNumber = intval(substr($oib, 10, 1), 10)))
        {
            $this->message='OIB is not mathematically correct!';
            return false;
        }
        return true;
    }
   
}