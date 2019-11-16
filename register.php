<?php
$response = array();
include 'db/db_connect.php';
include 'db/functions.php';
 
//pegar inputs do json
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //conversão do json para array
 
if(isset($input['username']) && isset($input['password']) && isset($input['full_name'])){
	$username = $input['username'];
	$password = $input['password'];
	$fullName = $input['full_name'];
	
	if(!userExists($username)){
 
		$salt         = getSalt();
		
		$passwordHash = password_hash(concatPasswordWithSalt($password,$salt),PASSWORD_DEFAULT);
		
		$insertQuery  = "INSERT INTO member(username, full_name, password_hash, salt) VALUES (?,?,?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("ssss",$username,$fullName,$passwordHash,$salt);
			$stmt->execute();
			$response["status"] = 0;
			$response["message"] = "User created";
			$stmt->close();
		}
	}
	else{
		$response["status"] = 1;
		$response["message"] = "User exists";
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
echo json_encode($response);
?>