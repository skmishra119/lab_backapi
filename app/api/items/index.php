<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

global $lab_id;
// item get by id API 
$app->get('/api/items/{lids}', function($request){	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);

	$qry="select i.id, i.name, i.description, i.unit, concat(i.minval,' - ',i.maxval) as vals, p.name as product, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_items i left join bl_products p on i.product_id=p.id where i.status='ACTIVE'";
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

// Single item by id
$app->get('/api/item/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$itm_id = trim($lu_ids[1]);
	$qry="select i.id, i.name, i.description, i.unit, i.minval, i.maxval, i.product_id, i.status, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_items i where i.id='".$itm_id."' and i.status='ACTIVE'";
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
			//$data['data'] = array(array('token'=>null));
			$data['message'] = array('type'=>'Error', 'msg'=>'No data available!');	
		}
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

// item get by product id API 
$app->get('/api/items/product/{lids}', function($request){	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$product_id = trim($lu_ids[1]);
	
	$qry="select i.id, i.name, i.description, i.unit, i.minval, i.maxval, p.name as product, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_items i left join bl_products p on i.product_id=p.id where p.product_id=:product_id and i.status='ACTIVE'";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
		$stmt->execute();
		$data['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);

		$data['message'] = array('type'=>'success', 'msg'=>'Items by product id.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

// item add API 
$app->post('/api/item/{lids}', function($request){
	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();
	
	$name 			= $request->getParam('name');
	$description 	= $request->getParam('description');
	$product_id 	= $request->getParam('product_id');
	$unit 			= $request->getParam('unit');
	$minval			= $request->getParam('minval');
	$maxval			= $request->getParam('maxval');
	$status 		= $request->getParam('status');
	// $qry 			= "insert into bl_items (name, description, product_id, status) values ( :name, :description, :product_id, :status)";
	$qry = "INSERT INTO bl_items (id, name, description, product_id, unit, minval, maxval, status) values (:newId, :name, :description, :product_id, :unit, :minval, :maxval, :status)";	
	
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':description', $description, PDO::PARAM_STR);
		$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
		$stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
		$stmt->bindParam(':minval', $minval, PDO::PARAM_STR);
		$stmt->bindParam(':maxval', $maxval, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$data['message'] = array('type'=>'success', 'msg'=>'Insert Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

// item update API 
$app->put('/api/item/{lids}', function($request){

	// $lab_id = '4d5b7f24-0b5e-11e9-89cd-0208c7f15232';
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$itm_id = trim($lu_ids[1]);

	$name 			= $request->getParam('name');
	$description 	= $request->getParam('description');
	$product_id 	= $request->getParam('product_id');
	$unit 			= $request->getParam('unit');
	$minval			= $request->getParam('minval');
	$maxval			= $request->getParam('maxval');
	$status 		= $request->getParam('status');
	$qry = "UPDATE bl_items	SET name = :name, description = :description, product_id = :product_id, unit = :unit, minval = :minval,  maxval = :maxval, status = :status WHERE id = :itm_id";
	
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':itm_id', $itm_id, PDO::PARAM_STR);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':description', $description, PDO::PARAM_STR);
		$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
		$stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
		$stmt->bindParam(':minval', $minval, PDO::PARAM_STR);
		$stmt->bindParam(':maxval', $maxval, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$data['message'] = array('type'=>'success', 'msg'=>'Update Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

// item Delete API 
$app->delete('/api/item/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$itm_id = trim($lu_ids[1]);
	// $lab_id = '4d5b7f24-0b5e-11e9-89cd-0208c7f15232';
	//$id 	= $request->getAttribute('id');
	$status = $request->getParam('status');
	$qry 	= "UPDATE bl_items SET status = 'DELETED' WHERE id = :itm_id";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);		
		$stmt->bindParam(':itm_id', $itm_id, PDO::PARAM_STR);
		$stmt->execute();
		$data['message'] = array('type'=>'success', 'msg'=>'Deleted successfully');	
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});	