<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

// item add API 
$app->post('/api/order_processing/{lids}', function($request){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$patient_id = $request->getParam('patient_id');
	$referer_id = $request->getParam('referer_id');
	$status = $request->getParam('status');
	$items = !empty($request->getParam('items')) ? $request->getParam('items') : [];

	$qry="insert into bl_orders_processing (id, patient_id, referer_id, status) values (:newid, :patient_id, :referer_id, :status)";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':newid', $newId, PDO::PARAM_STR);
		$stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
		$stmt->bindParam(':referer_id', $referer_id, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$order_id = $newId;
		// Insertion into orders item table
		foreach ($items as $item) {
			$newId = $IdConf->generate();
			$item_id = $item['id'];
			$item_name = $item['item_name'];
			$min_value = $item['min_value'];
			$max_value = $item['max_value'];
			$current_value = $item['current_value'];

			$qry="insert into bl_order_items_processing (id, order_id, item_id, item_name, min_value, max_value, current_value) values (:newId, :order_id, :item_id, :item_name, :min_value, :max_value, :current_value)";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
			$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
			$stmt->bindParam(':item_id', $item_id, PDO::PARAM_STR);
			$stmt->bindParam(':item_name', $item_name, PDO::PARAM_STR);
			$stmt->bindParam(':min_value', $min_value, PDO::PARAM_STR);
			$stmt->bindParam(':max_value', $max_value, PDO::PARAM_STR);
			$stmt->bindParam(':current_value', $current_value, PDO::PARAM_STR);
			$stmt->execute();
		}

		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Saved Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

// Single order_processing by id
$app->get('/api/order_processing/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pid = trim($lu_ids[1]);
	$qry = "select id, patient_id, referer_id, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_orders_processing where id='$pid' AND status='ACTIVE'";
	
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
			$order_info = (array)current($data['data']);
			$order_info['items'] = array();
			$qry = "select id, item_name, min_value, max_value, current_value, date_format(created,'%b %d, %Y %H:%i:%s') as created FROM bl_order_items_processing where order_id='$pid'"; // Need to join with item table to get item more info

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->query($qry);
			$order_info['items'] = $stmt->fetchAll(PDO::FETCH_OBJ);
			$data['data'] = $order_info;
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

// order_processing update API 
$app->put('/api/order_processing/{lids}', function($request){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$oid = trim($lu_ids[1]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$patient_id = $request->getParam('patient_id');
	$referer_id = $request->getParam('referer_id');
	$status = $request->getParam('status');
	$items = !empty($request->getParam('items')) ? $request->getParam('items') : [];

	$qry="UPDATE bl_orders_processing SET patient_id = :patient_id, referer_id = :referer_id, status = :status WHERE id = :oid";

	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
		$stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
		$stmt->bindParam(':referer_id', $referer_id, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();
		
		$qry = "DELETE FROM bl_order_items_processing WHERE order_id = :oid";
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);		
		$stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
		$stmt->execute();

		$order_id = $oid;
		// Insertion into orders item table
		foreach ($items as $item) {
			$newId = $IdConf->generate();
			$item_id = $item['id'];
			$item_name = $item['item_name'];
			$min_value = $item['min_value'];
			$max_value = $item['max_value'];
			$current_value = $item['current_value'];

			$qry="insert into bl_order_items_processing (id, order_id, item_id, item_name, min_value, max_value, current_value) values (:newId, :order_id, :item_id, :item_name, :min_value, :max_value, :current_value)";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
			$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
			$stmt->bindParam(':item_id', $item_id, PDO::PARAM_STR);
			$stmt->bindParam(':item_name', $item_name, PDO::PARAM_STR);
			$stmt->bindParam(':min_value', $min_value, PDO::PARAM_STR);
			$stmt->bindParam(':max_value', $max_value, PDO::PARAM_STR);
			$stmt->bindParam(':current_value', $current_value, PDO::PARAM_STR);
			$stmt->execute();
		}

		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Saved Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

