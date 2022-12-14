<?php 
	session_start();

	$servername = "localhost";
	$username = "root";
	$password = "";

	$uname = $_POST['uid'];
	$pass = $_POST['pass'];
	$stud = $_POST['student'];

	if($stud === '1'){
		$dbname = "test";
		try {

			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$s = "SELECT salt, hash FROM creds WHERE uid = :uname";
			$stmt = $conn->prepare($s);
			$stmt->bindValue(':uname', $uname);
			$stmt->execute();

			$result = $stmt->fetchAll();
			$no = sizeof($result);
			if($no == 1){
				$row = $result[0];
				$salt = $row['salt'];
				$h = $row['hash'];
				$calc = hash_pbkdf2('sha256', $pass, $salt, 100000);
				if($calc == $h){
					$_SESSION['logged_in'] = $uname;
					$_SESSION['student'] = $stud;
					echo "S";
				}
				else{
					echo "N";
				}
			}
			elseif($no == 0){
				echo "N";
			}
			else{
				echo "N";
			}
		}
		catch(PDOException $e){
			echo $e;
			echo "D";
		}
		$conn = null;
	}
	else if($stud === '0'){
		$dbname = "test";
		try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$s = "SELECT salt, hash FROM instructor WHERE uid = :uname";
			$stmt = $conn->prepare($s);
			$stmt->bindValue(':uname', $uname);
			$stmt->execute();

			// set the resulting array to associative
			// $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetchAll();
			$no = sizeof($result);
			if($no == 1){
				$row = $result[0];
				$salt = $row['salt'];
				$h = $row['hash'];
				$calc = hash_pbkdf2('sha256', $pass, $salt, 100000);
				if($calc == $h){
					$_SESSION['logged_in'] = $uname;
					$_SESSION['student'] = $stud;
					echo "S";
				}
				else{
					echo "N";
				}
			}
			elseif($no == 0){
				echo "U";
			}
			else{
				echo "N";
			}
		}
		catch(PDOException $e){
			echo $e;
			echo "D";
		}
		$conn = null;
	}

?>