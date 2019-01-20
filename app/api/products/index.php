<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

global $lab_id;
// product get by id API 
$app->get('/api/products/{lids}', function($request){	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	//$uid = trim($lu_ids[1]);

	$qry="select p.id, p.name, p.description, c.name as category, date_format(p.updated,'%b %d, %Y %H:%i:%s') as updated from bl_products p left join bl_categories c on p.category_id=c.id and c.status='ACTIVE' where p.status='ACTIVE'";
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

// Single product by id
$app->get('/api/product/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$pid = trim($lu_ids[1]);
	$qry="select id, name, description, category_id, status,  date_format(updated,'%b %d, %Y %H:%i:%s') as updated from bl_products where id='$pid' AND status='ACTIVE'";
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

// product get by parent id API 
$app->get('/api/products/category/{lids}', function($request){	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$category_id = trim($lu_ids[1]);
	
	$qry="SELECT * FROM bl_products WHERE category_id=:category_id";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
		$stmt->execute();
		$data['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);

		$data['message'] = array('type'=>'success', 'msg'=>'Parent products.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

// product add API 
$app->post('/api/product/{lids}', function($request){
	
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	
	$IdConf = new uuid_config();
	$newId = $IdConf->generate();
	
	$name 			= $request->getParam('name');
	$description 	= $request->getParam('description');
	$category_id 		= $request->getParam('category_id');
	$status 		= $request->getParam('status');
	// $qry 			= "insert into bl_products (name, description, category_id, status) values ( :name, :description, :category_id, :status)";
	$qry 			= "INSERT INTO bl_products 
								SET id = :newId, 
								name = :name, 
				            	description = :description, 
				            	category_id = :category_id,  
				            	status = :status";	
	
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
		$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->execute();

		$data['message'] = array('type'=>'success', 'msg'=>'Insert Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

// product update API 
$app->put('/api/product/{lids}', function($request){

	// $lab_id = '4d5b7f24-0b5e-11e9-89cd-0208c7f15232';
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$cid = trim($lu_ids[1]);

	$name 			= $request->getParam('name');
	$description 	= $request->getParam('description');
	$category_id 		= $request->getParam('category_id');
	$status 		= $request->getParam('status');
	$qry 			= "UPDATE bl_products
								SET name = :name, 
				            		description = :description, 
				            		category_id = :category_id,  
				            		status = :status 
				            	WHERE id = :cid";
	
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':description', $description, PDO::PARAM_STR);
		$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
		$stmt->execute();

		$data['message'] = array('type'=>'success', 'msg'=>'Update Successfully.');	
		echo json_encode(array_reverse($data));	
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
	
});

// product Delete API 
$app->delete('/api/product/{lids}', function($request, $response, $args){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$cid = trim($lu_ids[1]);
	// $lab_id = '4d5b7f24-0b5e-11e9-89cd-0208c7f15232';
	$id 	= $request->getAttribute('id');
	$qry 	= "UPDATE bl_products SET status = 'DELETED' WHERE id = :cid";
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->prepare($qry);		
		$stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
		$stmt->execute();
		$data['message'] = array('type'=>'success', 'msg'=>'Deleted successfully');	
		echo json_encode(array_reverse($data));
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});	