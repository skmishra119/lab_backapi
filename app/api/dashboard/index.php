<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

$app->get('/api/orders/current-month/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$usr_Id = trim($lu_ids[1]);
	$str_dt = date('Y-m-d', date('01-m-Y'));
	$end_dt = date('Y-m-d');
	
	$qry="select status, count(*) as total from bl_orders where status != 'DELETED' group by status";
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