<?php
require_once('course.php');
$year = $_GET["year"];
$class = $_GET["course"];

?>
<!DOCTYPE html>
<html>
<head><title>Course Taught by a Professor (BETAWEB)</title></head>
<body>
<h1>TuesdayNight on Betaweb</h1>
<h2>TA Lister</h2>
<p> Would you like to look up the TAs for another CSC course?</p>
<form action="get-courses.php" method="get">
Course: <input type="text" name="course"  placeholder="<?php echo $class?>" ><br>
Year: <input type="text" name="year"  placeholder="<?php echo $year?>" ><br>


<input type="submit">

<?php

echo $class;
 echo '<table cellspacing="1"><thead><th width="150">CRN</th><th width="150">Year</th><th width="250">Semester</th><th>Dept</th></thead><tbody>';
$courses = COURSE::getByCoursesNetid($class);
    foreach ($courses as $course) {
        echo "<tr><td>{$course->getCrn()}</td><td>{$course->getYear()}</td><td>{$course->getSemester()}</td><td>{$course->getDepartment()}</td></tr>";
    }
    echo '</tbody></table>';

?>


</body>
</html>

