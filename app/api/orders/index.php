<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;

// item add API 
$app->post('/api/order/{lids}', function($request){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$patient_id = $request->getParam('patient_id');
	$referer_id = $request->getParam('referer_id');
	$status = $request->getParam('status');
	$items = !empty($request->getParam('items')) ? $request->getParam('items') : [];

	$qry="insert into bl_orders (id, patient_id, referer_id, status) values (:newid, :patient_id, :referer_id, :status)";
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

		// Insertion into orders item table
		foreach ($items as $item) {
			$newId = $IdConf->generate();
			$item_id = $item;
			$qry="insert into bl_order_items (id, order_id, item_id) values (:newId, :order_id, :item_id)";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
			$stmt->bindParam(':order_id', $newId, PDO::PARAM_STR);
			$stmt->bindParam(':item_id', $item_id, PDO::PARAM_STR);
			$stmt->execute();
		}

		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Saved Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

