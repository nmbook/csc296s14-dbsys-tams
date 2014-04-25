<?php
/**
 * This is the DAL for the Instructor class.
 *
 * Currently functional (and tested) parts:
 * string $ins->getNetID()
 * string $ins->getName()
 * string $ins->getEmail()
 * int $ins->getOffice()
 */

$dbname = 'nbook';
require_once('../dbsetup.php');


class Instructor
{
    private $netid;
    private $name;
    private $email;
    private $office;
    private $classes;

    static private function getMapping($sql,$arr,$callback) {
        global $db;
        $stmt = $db->prepare($sql);
        $stmt->execute($arr);
        $stmt->setFetchMode(PDO::FETCH_NUM);
        return array_map($callback,$stmt);
    }

    public function __construct($row) {
        $this->netid = $row['netid'];
        $this->name = $row['name'];
        $this->office = $row['office'];
        if (array_key_exists('email',$row))
            $this->email = $row['email'];
        else
            $this->email = $row['name'] . "@u.rochester.edu";
        $this->classes = NULL;
    }

    public function getNetID() {return $this->netid;}
    public function getName()  {return $this->name;}
    public function getEmail() {return $this->email;}
    public function getOffice() {return $this->office;}

    public function getClasses() {
        if ($this->classes == NULL)
            $this->classes = Instructor::getMapping("SELECT * FROM Teaches INNER JOIN Courses
            ON Teaches.course = Courses.id
            WHERE
            Teaches.instructor = :netid",
            array(':netid' => $this->netid),
            function ($x) { return Course($x); });
        return $this->classes;
    }

    static public function getByNetID($netid) {
        global $db;
        $stmt = $db->prepare('SELECT * FROM Instructors WHERE netid=:netid;');
        $stmt->execute(array(':netid' => $netid));
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return new Instructor($stmt->fetch());
    }
	

	public function assignCourse($crn) {
		Utils::getVoid('Update Teaches SET instructor = :netid WHERE crn = :crn',
			array(':netid' => $this-> netid,
			':crn' => $crn));
		update();
	}

	static public function getCount() {
        global $db;
        $stmt = $db->prepare('SELECT COUNT(*) FROM Instructors');
        if (!$stmt->execute(array())) {
            var_dump( $stmt->errorInfo());
            exit;
        }
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $result = $stmt->fetch();
        return $result[0] + 0;
    }

	static public function import($arr) {
		foreach ($arr as $row) {
			$row2 = array();
			foreach ($row as $key => $value) {
				$row2[':' . $key] = $value;	
			}
			Utils::getVoid('INSERT INTO instructors (netid,name,email,office_room) VALUES (:netid,:name,:email,:office_room)',
				$row2);
		}
	}


}
