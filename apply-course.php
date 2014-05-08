<!DOCTYPE html>
<html>
<head><title>Apply to a Course - TA Management System</title></head>
<body>
<h1>TA Management System on Betaweb</h1>
<h2>Apply to a course</h2>
<?php
require_once('course.php');
require_once('ta.php');

function render() {
	if (!isset($_COOKIE['netid']) || !isset($_COOKIE['password'])) {
		echo "netid: {$_COOKIE['netid']} and password: {$_COOKIE['password']}";
?>
		<p>You are not logged in. Go <a href="index.php">here</a> to login</p>
<?php
		return;
	}
	$netid = $_COOKIE['netid'];
	$password = $_COOKIE['password'];
	try {
		$ta = TA::getByCredentials($netid,$password);
	}
	catch (Exception $e) {
		echo "<p>ERROR: a TA with netid $netid and password $password is not in our database</p>\n";
		return;
	}
	if (!isset($_POST['for_credit']) || (!isset($_POST['crn']) && 
		!(isset($_POST['dept']) && isset($_POST['number']) && isset($_POST['year']) && isset($_POST['semester'])))) {
?>
    <form action="apply-course.php" method="post">
    <label for="netid">Netid:</label>
    <input type="text" name="netid" id="netid"></input><br/>
    <label for="for_credit">For Credit?:</label>
    <select name="for_credit" id="for_credit"><option value="1">Yes</option><option value="0">No</option></select><br/>
    <label for="dept">Course Department:</label>
    <input type="text" name="dept" id="dept"></input><br/>
    <label for="number">Course Number:</label>
    <input type="text" name="number" id="number"></input><br/>
    <label for="year">Year:</label>
    <input type="text" name="year" id="year"></input><br/>
    <label for="semester">Semester:</label>
    <input type="text" name="semester" id="semester"></input><br/>
    <input type="submit"></input>
    </form>
<?php
		return;
	}
	if (isset($_POST['crn'])) {
        $crn = $_POST['crn'];
    }
    else {
        $dept = $_POST['dept'];
        $number= $_POST['number'];
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        try {
            $crn = Course::getCourseByName($dept,$number,$year,$semester)->getCrn();
        }
        catch (Exception $e) {
            echo "<p>ERROR: there is no $dept $number class in $semester $year</p>\n";
            return;
        }
    }
	$for_credit = $_POST['for_credit'];
    try {
		$ta->applyCourse($crn,$for_credit);
		echo "<p>Course application sucessful!</p>\n";
	}
	catch (Exception $e) {
		echo "<p>ERROR: course application failed. Perhaps you already applied for this course? {$e->getMessage()}</p>\n";
	}
}
render();
?>
<a href=".">&lt-- Back</a>
</body>
</html>

