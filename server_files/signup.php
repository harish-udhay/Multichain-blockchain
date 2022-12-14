<?php 

	require __DIR__ . '/vendor/autoload.php';
	use be\kunstmaan\multichain\MultichainClient as MultichainClient;

	$servername = "localhost";
	$dbname = "test";
	$username = "root";
	$password = "";

	$uname = $_POST['uid'];
	$pass = $_POST['pass'];
	$pubkey = $_POST['pubkey'];

	$salt = bin2hex(random_bytes(32));

	$hash = hash_pbkdf2("sha256", $pass, $salt, 100000);

	try {
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $s = "SELECT * from instructor WHERE uid = :uname";
    	$stmt = $conn->prepare($s);
    	$stmt->bindValue(':uname', $uname);
    	$stmt->execute();
    	$result = $stmt->fetchAll();
		if(sizeof($result) > 0){
			echo "M";
		}
		else{
			echo "S";
		    $s = "INSERT INTO instructor VALUES (:uname, :salt, :hash)";
			$stmt = $conn->prepare($s);	
			$stmt->bindValue(':uname', $uname);
			$stmt->bindValue(':salt', $salt);
			$stmt->bindValue(':hash', $hash);
			
			if($stmt->execute()){
				$client = new MultichainClient("http://127.0.0.1:7718", "multichainrpc", "Bcx7e3sKyroteVnJ9t5NvCW1HojygcUyUrHXDibPQJ2Z", 3);
				$client->publishStreamItem('pubkey', $uname, $pubkey);
			}
		}
	}
	catch(PDOException $e){
		echo $e;
		echo "N";
	}
	$conn = null;
?>