<?php

/**
 * This is the DAL for the TA class.
 *
 * Currently functional (and tested) parts:
 * int TA::getCount() -- gets the total number of rows in the data
 * array TA::getByRange() -- gets an array of objects, one TA object per row.
 * string $ta->getNetID()
 * string $ta->getName()
 * string $ta->getEmail()
 * int $ta->getClassYear()
 */

//$dbname = 'nbook';
require_once('../dbsetup.php');
require_once('utils.php');

class TA {
    private $netid;
    private $name;
    private $email;
    private $class_year;
    private $applications;

    public function __construct($row) {
        $this->netid = $row['netid'];
        $this->name = $row['name'];
        $this->class_year = $row['class_year'];
        if (array_key_exists('email',$row))
            $this->email = $row['email'];
        else
            $this->email = $row['name'] . "@u.rochester.edu";
        $this->applications = NULL;
    }

    public function getNetID() {return $this->netid;}
    public function getName()  {return $this->name;}
    public function getEmail() {return $this->email;}
    public function getClassYear() {return intval($this->class_year);}

    public function getApplications() {
        if ($this->applications == NULL)
            $this->applications = Utils::getMapping("SELECT * FROM applications WHERE ta_id = :netid",
            array(':netid' => $this->netid),
            function ($x) { return Application($x); });
        return $this->applications;
    }

	public function update() {
		$this->applications = NULL;
	}

	public function applyCourse($crn,$forCredit) {
		Utils::getVoid('INSERT INTO applications (crn,ta_id,time_signup,time_response,state,for_credit) VALUES (:crn,:netid,:signup,:response,:state,:credit)',
			array(':coursecrn' => $crn,
			':netid' => $this->netid,
			':signup' => $_SERVER['REQUEST_TIME'],
			':response' => NULL,
			':state' => 'pending',
			':credit' => $forCredit));
		update();
	}

	public function applyWorkshop($crn,$forCredit) {
        Utils::getVoid('INSERT INTO workshop_apps (crn,ta_id,time_signup,time_response,state,for_credit) VALUES (:crn,:netid,:signup,:response,:state,:credit)',
            array(':coursecrn' => $crn,
            ':netid' => $this->netid,
            ':signup' => $_SERVER['REQUEST_TIME'],
            ':response' => NULL,
            ':state' => 'pending',
            ':credit' => $forCredit));
        update();
    }

    static public function getByNetID($netid) {
        return Utils::getSingle('SELECT netid,name,email,class_year FROM tas WHERE netid=:netid',
        	array(':netid' => $netid),
        	function ($x) { return TA($x); });
    }

    static public function getByRange($start,$len) {
        return Utils::getMapping('SELECT netid,name,email,class_year FROM tas LIMIT :start, :len',
            array(),
            function ($x) { return new TA($x); },
            $start,$len);
    }

    static public function getCount() {
        global $db;
        $stmt = $db->prepare('SELECT COUNT(*) FROM tas');
        if (!$stmt->execute(array())) {
            var_dump( $stmt->errorInfo());
            exit;
        }
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $result = $stmt->fetch();
        return $result[0] + 0;
    }
}
