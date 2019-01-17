<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

$app->get('/api/users/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);
	$qry="select u.id as user_id, u.email_id, concat(u.title,' ',u.first_name,' ',u.last_name) as fullname, date_format(u.updated,'%b %d, %Y %H:%i:%s') as updated, r.name as role from bl_users u left join bl_user_roles ur on u.id=ur.user_id left join bl_roles r on ur.role_id=r.id where u.status='ACTIVE'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data = $stmt->fetchAll(PDO::FETCH_OBJ);
		$lab_db = null;
		echo json_encode($data);
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->get('/api/roles/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);
	$qry="select r.id, r.name from bl_roles r where r.status='ACTIVE'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data = $stmt->fetchAll(PDO::FETCH_OBJ);
		$lab_db = null;
		echo json_encode($data);
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});


$app->get('/api/user/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);
	$qry="select u.id, u.email_id, u.title, u.first_name, u.last_name, date_format(u.updated,'%b %d, %Y %H:%i:%s') as updated, r.id as roleid, r.name as role, u.status from bl_users u left join bl_user_roles ur on u.id=ur.user_id left join bl_roles r on ur.role_id=r.id where u.status='ACTIVE' and u.id='".$uid."'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
		$lab_db = null;
		if(sizeof($data['data'])>0){
			$data['message'] = array('type'=>'success', 'msg'=>'Success');
		} else {
			$data['data'] = array(array('token'=>null));
			$data['message'] = array('type'=>'Error', 'msg'=>'No data available!');	
		}
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->post('/api/user/auth', function($request, $response, $args){
	$email = $request->getParsedBody()['email_id'];
	$pass =  $request->getParsedBody()['password'];
	$LABS = new lab_config();
	$LABS->bLab_id = $request->getParsedBody()['lab_id'];
	//$db = new lab_db();
	//$db = $db->connect($LABS->bLab_id);
	//$lab_id = $request->getParsedBody()['lab_id'];
	$qry="select id, id as token, concat(title,' ',first_name,' ',last_name) as fullname, '".$LABS->bLab_id."' as lab_id from bl_users where email_id='".$email."' and password='".md5($pass)."' and status='ACTIVE'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($LABS->bLab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		//echo  json_encode($lab_db);
		$stmt = $lab_db->query($qry);
		$data['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
		//echo json_encode($data);
		$lab_db = null;
		if(sizeof($data['data'])>0){
			$data['message'] = array('type'=>'success', 'msg'=>'Success');	
			echo json_encode(array_reverse($data));
		} else {
			$data['data'] = array(array('token'=>null));
			$data['message'] = array('type'=>'Error', 'msg'=>'Authentication failed.');	
			echo json_encode(array_reverse($data));
		}
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}'; exit;
	}
	
});



$app->post('/api/user/{lids}', function($request, $response, $args){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$password = md5('Welcome@123');
	$role = $request->getParam('role');
	$status = $request->getParam('status');
	$qry="insert into bl_users (id, title, first_name, last_name, email_id, password, status) values (:newid, :title, :first_name, :last_name, :email_id, :password, :status)";

	$user_role_id = $IdConf->generate();
	$roleQry = "insert into bl_user_roles (id, user_id, role_id) value (:id, :user_id, :role_id)";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':newid', $newId, PDO::PARAM_STR);
		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
		$stmt->bindParam(':first_name', $fname, PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $lname, PDO::PARAM_STR);
		$stmt->bindParam(':email_id', $email, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();
		
		$stmt = $lab_db->prepare($roleQry);
		$stmt->bindParam(':id', $user_role_id, PDO::PARAM_STR);
		$stmt->bindParam(':role_id', $role, PDO::PARAM_STR);
		$stmt->bindParam(':user_id', $newId, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Registered Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

$app->put('/api/user/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$usrid = trim($lu_ids[1]);
	//$usrid = $request->getAttribute('id');
	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$status = $request->getParam('status');
	$role = $request->getParam('role');

	$uQry = "update bl_users set title = :title, first_name = :fname, last_name = :lname, email_id = :emailid, status = :status where id='".$usrid."'";
	$rQry = "update bl_user_roles set role_id = :role_id where user_id='".$usrid."'";
	try{

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($uQry);
		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
		$stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
		$stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
		$stmt->bindParam(':emailid', $email, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$stmt = $lab_db->prepare($rQry);
		$stmt->bindParam(':role_id', $role, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Updated successfully');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->delete('/api/user/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);
	$uQry="update bl_users set status='DELETED' where id='".$uid."'";
	$rQry="update bl_user_roles set status='DELETED' where user_id='".$uid."'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($uQry);
		$stmt->execute();
		$stmt = $lab_db->prepare($rQry);
		$stmt->execute();
		$data['message'] = array('type'=>'success', 'msg'=>'Deleted successfully');	
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});