<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$app->get('/api/labs', function(){
	$qry="select * from lab_master where status='ACTIVE'";
	try{

		$db = new db();
		$db = $db->connect();
		$stmt = $db->query($qry);
		$data = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($data);
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->get('/api/lab_connect/{id}', function($request){
	$lab_id = $request->getAttribute('id');
	$qry="select * from lab_master where id='".$lab_id."'";
	try{

		$db = new db();
		$db = $db->connect();
		$stmt = $db->query($qry);
		$data['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		if(sizeof($data['data'])>0){
			$data['message'] = array('type'=>'success', 'msg'=>'Success');
		} else {
			$data['data'] = array(array('token'=>null));
			$data['message'] = array('type'=>'Error', 'msg'=>'No data found.');	
		}
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

