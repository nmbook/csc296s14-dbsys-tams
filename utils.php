<?php
require_once('../dbsetup.php');
class Utils {
	private static function debug($stmt,$arr) {
		if (empty($arr)) {
			$guard = $stmt->execute();	
		}
		else {
			$guard = $stmt->execute($arr);
		}
		if (!$guard) {
			print_r($stmt->errorInfo());
			exit;	
		}
	}

	public static function prepareArray($row) {
		$row2 = array();
		foreach ($row as $key => $value) {
			$row2[':' . $key] = $value;
		}
		return $row2;
	}

	public static function getMapping($sql,$arr,$callback,$limit_start = null,$limit_len = null) {
    	    global $db;
    	    $stmt = $db->prepare($sql);
    	    if ($limit_start !== null && $limit_len !== null) {
    	        $stmt->bindParam(':start',intval($limit_start),PDO::PARAM_INT);
    	        $stmt->bindParam(':len',intval($limit_len),PDO::PARAM_INT);
    	    }
    	    Utils::debug($stmt,$arr);
    	    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    	    return array_map($callback,$stmt->fetchAll());//$stmt->fetchAll());
    }

	public static function getSingle($sql,$arr,$callback) {
		global $db;
		$stmt = $db->prepare($sql);
		Utils::debug($stmt,$arr);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $callback($stmt->fetch());
	}

	public static function getVoid($sql,$arr,$ignoreError=false) {
		global $db;
		$stmt = $db->prepare($sql);
		if (!$ignoreError) Utils::debug($stmt,$arr);
	}
}
?>
