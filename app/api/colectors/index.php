<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

$app->get('/api/colectors/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);
	$qry="select p.id, p.email_id, concat(p.title,' ',p.first_name,' ',p.last_name) as fullname, p.address, p.city, p.mobile, date_format(p.updated,'%b %d, %Y %H:%i:%s') as updated from bl_colectors p where p.status='ACTIVE'";
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


$app->get('/api/colector/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pat_id = trim($lu_ids[1]);
	$qry="select p.id, p.email_id, p.title, p.first_name, p.last_name, p.address, p.city, p.mobile, p.status, date_format(p.updated,'%b %d, %Y %H:%i:%s') as updated from bl_colectors p where p.status='ACTIVE' and p.id='".$pat_id."'";
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

// Add cotecor
$app->post('/api/colector/{lids}', function($request, $response, $args){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pat_id = trim($lu_ids[1]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$addr =  $request->getParam('address');
	$city = $request->getParam('city'); 
	$mobile = $request->getParam('mobile');
	//$password = md5('Welcome@123');
	//$role = $request->getParam('role');
	$status = $request->getParam('status');
	$qry="insert into bl_colectors (id, title, first_name, last_name, email_id, address, city, mobile, status) values (:newid, :title, :first_name, :last_name, :email_id, :address, :city, :mobile, :status)";
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


// Update cotecor
$app->put('/api/colector/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pat_id = trim($lu_ids[1]);
	//$usrid = $request->getAttribute('id');
	$title = $request->getParam('title');
	$fname = $request->getParam('first_name');
	$lname = $request->getParam('last_name');
	$email = $request->getParam('email_id');
	$addr =  $request->getParam('address');
	$city = $request->getParam('city'); 
	$mobile = $request->getParam('mobile');
	$status = $request->getParam('status');
	
	$uQry = "update bl_colectors set title = :title, first_name = :fname, last_name = :lname, email_id = :emailid, address = :address, city = :city, mobile = :mobile, status = :status where id='".$pat_id."'";
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

// Deleted record
$app->delete('/api/colector/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pat_id = trim($lu_ids[1]);
	$uQry="update bl_colectors set status='DELETED' where id='".$pat_id."'";
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