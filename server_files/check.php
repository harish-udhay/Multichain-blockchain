<?php
	require __DIR__ . '/vendor/autoload.php';
	use \be\kunstmaan\multichain\MultichainClient as MultichainClient;

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

	$s = 'SELECT * FROM grades ORDER BY txid asc';
	// $conn = null;
	$stmt = $conn->prepare($s);
	$stmt->execute();

	$cur_db = array();
	$primkey_list = array();
	$all_prev_tx = '';
	$tuple = '';
	
	$client = new MultichainClient("http://127.0.0.1:7718", "multichainrpc", "Bcx7e3sKyroteVnJ9t5NvCW1HojygcUyUrHXDibPQJ2Z", 3);
	if($stmt->rowCount() > 0){
		// $address = $client->setDebug(true)->getNewAddress();
		$latest_tx = $client->listStreamItems('stream1');

		$latest_txid = $latest_tx[0]['txid'];
		$hash = $latest_tx[0]['data'];

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if($row['txid'] === $latest_txid){
				$tuple = $row['uid'] . $row['course'] . $row['grade'] . $row['identifier'];
			}
			else{
				$all_prev_tx .= $row['txid'] . $row['uid'] . $row['course'] . $row['grade'] . $row['identifier'];
			}
			$cur_db[] = $row['txid'] . $row['uid'] . $row['course'] . $row['grade'] . $row['identifier'];
			$primkey_list[] = $row['uid'] . $row['course'];
		}

		$temp_hash = hash('sha256', $all_prev_tx);
		$calc = hash('sha256', $temp_hash . $tuple);
		
		if($calc !== $hash){
			echo "UFO";
			die();
		}
	}
?>