<?php 
	session_start();
	$servername = "localhost";
	// $username = "root";
	// $password = "Hack@hack1";
	$dbname = "test";
	$username = "root";
	$password = "";

	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e){
		$conn = null;
		echo "D" . "0";
		die();
	}

	if(isset($_SESSION['logged_in'])){
		if($_SESSION['student'] === '1'){ // student
			try {
				require __DIR__ . "/check.php"; 
			}
			catch (Exception $e){
				echo "D";
				die();
			}

			try {
				$s = "SELECT grades.uid id, name, course, grade FROM grades, creds WHERE grades.uid=creds.uid AND grades.uid = :uname";
				$stmt = $conn->prepare($s);
				$stmt->bindValue(':uname', $_SESSION['logged_in']);
				$stmt->execute();

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					echo $row['id'] . '%' . $row['name'] . '%' . $row['course'] . '%' . $row['grade'] . '&';
				}
			}
			catch(PDOException $e){
				echo "D";
			}
			$conn = null;
		}
		else if($_SESSION['student'] === '0'){ // instructor
			
			try {
				require __DIR__ . "/check.php"; 
			}
			catch (Exception $e){
				echo "D";
				die();
			}

			try {
				$identifier = $_SESSION['logged_in'];

				// $course_list = $client->listStreamKeyItems('instructor', $identifier, false, 999999999);
				$course_list = $client->liststreamitems('instructor', false, 999999999);
				$in = "";
				foreach ($course_list as $i => $value) {
					
					$key = ":id".$i;
					// $in .= "$key,";
					$in_params[$key] = pack('H*', $value['data']);
					$in .= "'{$in_params[$key]}',";
				}
				$in = rtrim($in,",");
				$s = "SELECT grades.uid id, name, course, grade FROM grades, creds WHERE grades.uid=creds.uid AND course IN ($in) ORDER BY course asc";
				$stmt = $conn->prepare($s);
				$stmt->execute();

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					echo $row['id'] . '%' . $row['name'] . '%' . $row['course'] . '%' . $row['grade'] . '&';
				}

			}
			catch(PDOException $e){
				echo $e;
				echo "D";
			}
			$conn = null;
		}
	}
?>