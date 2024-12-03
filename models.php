<?php
function fetchApplicants($pdo, $search = null) {
    $sql = "SELECT * FROM applicants";
    if ($search) {
        $sql .= " WHERE CONCAT_WS(' ', first_name, last_name, email, phone, address, job_title, skills, status) LIKE ?";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($search ? ["%$search%"] : []);
    return [
        'message' => 'Applicants fetched successfully.',
        'statusCode' => 200,
        'querySet' => $stmt->fetchAll()
    ];
}

function insertApplicant($pdo, $data) {
    try {
        $sql = "INSERT INTO applicants (first_name, last_name, email, phone, address, job_title, skills, status, added_by) 
                VALUES (:first_name, :last_name, :email, :phone, :address, :job_title, :skills, :status, :added_by)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return ['message' => 'Applicant added successfully.', 'statusCode' => 200];
    } catch (PDOException $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'statusCode' => 400];
    }
}

function updateApplicant($pdo, $id, $data) {
    try {
        $sql = "UPDATE applicants SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, 
                address = :address, job_title = :job_title, skills = :skills, status = :status, added_by = :added_by 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $data['id'] = $id;
        $stmt->execute($data);
        return ['message' => 'Applicant updated successfully.', 'statusCode' => 200];
    } catch (PDOException $e) {
        return ['message' => 'Error: ' . $e->getMessage(), 'statusCode' => 400];
    }
}

function deleteApplicant($pdo, $id) {
    try {
        $sql = "DELETE FROM applicants WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return [
            'message' => 'Applicant deleted successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Error deleting applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM user_accounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}

function insertNewUser($pdo, $username, $first_name, $last_name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO user_accounts (username, first_name, last_name, password) 
		VALUES (?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllApplicants($pdo) {
	$sql = "SELECT * FROM applicants";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllApplicantsBySearch($pdo, $search_query) {
	$sql = "SELECT * FROM applicants WHERE 
			CONCAT(first_name,last_name,
            email,phone,address,job_title,skills,
            status,added_by,last_updated) 
			LIKE ?";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute(["%".$search_query."%"]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getApplicantsByID($pdo, $id) {
	$sql = "SELECT * FROM applicants WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute([$id])) {
		return $stmt->fetch();
	}
}

function insertAnActivityLog($pdo, $operation, $id, $first_name, $last_name, $email,
    $phone,$address,$job_title,$skills,$status,$username) {

	$sql = "INSERT INTO activity_logs (operation, id, first_name, last_name, email,
    phone,address,job_title,skills,status,username) VALUES(?,?,?,?,?,?,?,?,?,?,?)";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$operation, $id, $first_name, $last_name, $email,
    $phone,$address,$job_title,$skills,$status,$username]);

	if ($executeQuery) {
		return true;
	}

}

function getAllActivityLogs($pdo) {
	$sql = "SELECT * FROM activity_logs 
			ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute()) {
		return $stmt->fetchAll();
	}
}

function insertAnApplicant($pdo, $first_name, $last_name, $email, $phone, $address, $job_title, $skills, $added_by) {
	$response = array();
	$sql = "INSERT INTO applicants (first_name,last_name,email,phone,address,job_title,skills,added_by) VALUES(?,?,?,?,?,?,?,?)";
	$stmt = $pdo->prepare($sql);
	$insertApplicant = $stmt->execute([$first_name, $last_name, $email, $phone, $address, $job_title, $skills, $added_by]);

	if ($insertApplicant) {
		$findInsertedItemSQL = "SELECT * FROM applicants ORDER BY date_added DESC LIMIT 1";
		$stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
		$stmtfindInsertedItemSQL->execute();
		$getApplicantID = $stmtfindInsertedItemSQL->fetch();

		$insertAnActivityLog = insertAnActivityLog($pdo, "INSERT", $getApplicantID['id'], 
        $getApplicantID['first_name'],$getApplicantID['last_name'],$getApplicantID['email'],$getApplicantID['phone'],
         $getApplicantID['address'], $_SESSION['username']);

		if ($insertAnActivityLog) {
			$response = array(
				"status" =>"200",
				"message"=>"Applicant added successfully!"
			);
		}

		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}
		
	}

	else {
		$response = array(
			"status" =>"400",
			"message"=>"Insertion of data failed!"
		);

	}

	return $response;
}


?>