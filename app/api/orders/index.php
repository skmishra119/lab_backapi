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

		$order_id = $newId;
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
			$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
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

// Single order by id
$app->get('/api/order/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pid = trim($lu_ids[1]);
	$qry = "select id, patient_id, referer_id, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_orders where id='$pid' AND status='ACTIVE'";
	
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
			$qry = "select id, date_format(created,'%b %d, %Y %H:%i:%s') as created FROM bl_order_items where order_id='$pid'"; // Need to join with item table to get item more info

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

//multiple order listing
$app->get('/api/orders/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);

	$qry = "select id, patient_id, referer_id, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_orders where status='ACTIVE'";
	
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

			$orders_data = array();
			foreach ($data['data'] as $key => $value) {
				$orders_data[0] = (array)$value;
				$orders_data[0]['items'] = array();
				$qry = "select id, date_format(created,'%b %d, %Y %H:%i:%s') as created FROM bl_order_items where order_id='$value->id'"; // Need to join with item table to get item more info

				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}
				$stmt = $lab_db->query($qry);
				$orders_data[0]['items'] = $stmt->fetchAll(PDO::FETCH_OBJ);
				
			}
			$data['data'] = $orders_data;
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

// order update API 
$app->put('/api/order/{lids}', function($request){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$oid = trim($lu_ids[1]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();

	$patient_id = $request->getParam('patient_id');
	$referer_id = $request->getParam('referer_id');
	$status = $request->getParam('status');
	$items = !empty($request->getParam('items')) ? $request->getParam('items') : [];

	$qry="UPDATE bl_orders SET patient_id = :patient_id, referer_id = :referer_id, status = :status WHERE id = :oid";

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


		$qry = "DELETE FROM bl_order_items WHERE order_id = :oid";
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
			$item_id = $item;
			$qry="insert into bl_order_items (id, order_id, item_id) values (:newId, :order_id, :item_id)";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
			$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
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

// order Delete API 
$app->delete('/api/order/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$oid = trim($lu_ids[1]);
	$id 	= $request->getAttribute('id');
	$qry 	= "UPDATE bl_orders SET status = 'DELETED' WHERE id = :oid";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);		
		$stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
		$stmt->execute();
		$data['message'] = array('type'=>'success', 'msg'=>'Deleted successfully');	
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});	

