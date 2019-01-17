<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

$app->get('/api/doctors/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);
	$qry="select d.id, d.email_id, concat(d.title,' ',d.first_name,' ',d.last_name) as fullname, d.clinic, d.address, d.city, d.mobile, date_format(d.updated,'%b %d, %Y %H:%i:%s') as updated from bl_doctors d where d.status='ACTIVE'";
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


$app->get('/api/doctor/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$doctor_id = trim($lu_ids[1]);
	$qry="select d.id, d.email_id, d.title, d.first_name, d.last_name, d.clinic, d.address, d.city, d.mobile, d.status, date_format(d.updated,'%b %d, %Y %H:%i:%s') as updated from bl_doctors d where d.status='ACTIVE' and d.id='".$doctor_id."'";
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

$app->post('/api/doctor/{lids}', function($request, $response, $args){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$clinic = $request->getParam('clinic');
	$addr =  $request->getParam('address');
	$city = $request->getParam('city'); 
	$mobile = $request->getParam('mobile');
	//$password = md5('Welcome@123');
	//$role = $request->getParam('role');
	$status = $request->getParam('status');
	$qry="insert into bl_doctors (id, title, first_name, last_name, email_id, clinic, address, city, mobile, status) values (:newid, :title, :first_name, :last_name, :email_id, :clinic, :address, :city, :mobile, :status)";
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
		$stmt->bindParam(':clinic', $clinic, PDO::PARAM_STR);
		$stmt->bindParam(':address', $addr, PDO::PARAM_STR);
		$stmt->bindParam(':city', $city, PDO::PARAM_STR);
		$stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();
		
		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Saved Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->put('/api/doctor/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$doctor_id = trim($lu_ids[1]);
	//$usrid = $request->getAttribute('id');
	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$clinic = $request->getParam('clinic');
	$addr =  $request->getParam('address');
	$city = $request->getParam('city'); 
	$mobile = $request->getParam('mobile');
	$status = $request->getParam('status');
	
	$uQry = "update bl_doctors set title = :title, first_name = :fname, last_name = :lname, email_id = :emailid, clinic = :clinic, address = :address, city = :city, mobile = :mobile, status = :status where id='".$doctor_id."'";
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
		$stmt->bindParam(':clinic', $clinic, PDO::PARAM_STR);
		$stmt->bindParam(':address', $addr, PDO::PARAM_STR);
		$stmt->bindParam(':city', $city, PDO::PARAM_STR);
		$stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Updated successfully');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->delete('/api/doctor/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$doctor_id = trim($lu_ids[1]);
	$uQry="update bl_doctors set status='DELETED' where id='".$doctor_id."'";
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