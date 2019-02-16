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
	$oid = trim($lu_ids[1]);

	// Getting order details first
	$order_info = null;
	$qry = "select id, barcode, date_format(order_date,'%d/%m/%Y') as order_date, patient_id, doctor_id, collector_id, barcode, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_orders where id='$oid' AND status='ACTIVE'";
	
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
			$order_info['prod_ids'] = array();
			$qry = "select id, product_id FROM bl_order_products where order_id='$oid'";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->query($qry);
			$order_info['prod_ids'] = $stmt->fetchAll(PDO::FETCH_OBJ);

			//Insetion bl_order_process table
			$IdConf = new uuid_config();
			$order_process_id = $IdConf->generate();
			$status = 'PROCESSING';
			$qry="insert into bl_order_process (id, order_id, status) values (:newid, :oid, :status)";
			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':newid', $order_process_id, PDO::PARAM_STR);
			$stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->execute();

			foreach ($order_info['prod_ids'] as $product) {

				$product_id = $product->product_id;

				// Insertion into bl_order_process_products table
				$qry = "INSERT INTO bl_order_process_products (id, order_process_id, product_id) values (:newId, :order_process_id, :product_id)";

				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}

				$IdConf = new uuid_config();
				$processProductId = $IdConf->generate();

				$stmt = $lab_db->prepare($qry);
				$stmt->bindParam(':newId', $processProductId, PDO::PARAM_STR);
				$stmt->bindParam(':order_process_id', $order_process_id, PDO::PARAM_STR);
				$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
				$stmt->execute();
				
				// Insertion into bl_order_process_items table

				$qry="select i.id, i.name, i.description, i.unit, i.minval, i.maxval, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_items i left join bl_products p on i.product_id=p.id where p.id=:product_id and i.status='ACTIVE'";

				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}
				$stmt = $lab_db->prepare($qry);
				$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
				$stmt->execute();
				$product_items = $stmt->fetchAll(PDO::FETCH_OBJ);

				foreach ($product_items as $value) {

					$name 			= $value->name;
					$description 	= $value->description;
					$unit 			= $value->unit;
					$minval			= $value->minval;
					$maxval			= $value->maxval;
					$qry = "INSERT INTO bl_order_process_items (id, order_process_product_id, name, description, product_id, unit, minval, maxval) values (:newId, :order_process_product_id, :name, :description, :product_id, :unit, :minval, :maxval)";

					$lab_db = new lab_db();
					$lab_db = $lab_db->connect($lab_id);
					if($lab_db==null) {
						throw new PDOException("Internal server error in connecting databases", 1);
					}

					$IdConf = new uuid_config();
					$newId = $IdConf->generate();

					$stmt = $lab_db->prepare($qry);
					$stmt->bindParam(':newId', $newId, PDO::PARAM_STR);
					$stmt->bindParam(':order_process_product_id', $processProductId, PDO::PARAM_STR);
					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->bindParam(':description', $description, PDO::PARAM_STR);
					$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
					$stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
					$stmt->bindParam(':minval', $minval, PDO::PARAM_STR);
					$stmt->bindParam(':maxval', $maxval, PDO::PARAM_STR);
					$stmt->execute();

				}
			}

			// At last updat the order table
			$status = "PROCESSING";
			$qry="UPDATE bl_orders SET status = :status WHERE id = :oid";

			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_STR);
			$stmt->execute();

			$data['data'] = array(array('token'=>null));
			$data['message'] = array('type'=>'success', 'msg'=>'Order Processed Successfully.');	
			echo json_encode(array_reverse($data));	

		} else {
			// Order does not longer exist
			echo '{"message" : {"type": "Error", "msg": "Order does not longer exist or has already been processed"}}';
		}
	} catch(PDOException $e){
		echo '{"message" : {"type": "Error", "msg": "'.$e->getMessage().'"}}';
	}

});

// Single order_processing by id
$app->get('/api/order_processing/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$oid = trim($lu_ids[1]);
	$qry = "select op.id, o.id as order_id, date_format(o.order_date,'%b %d, %Y') as order_date, concat(p.title,' ',p.first_name,' ',p.last_name) as patient, concat(d.title,' ',d.first_name,' ',d.last_name) as doctor, concat(c.title,' ',c.first_name,' ',c.last_name) as collector, o.barcode, op.status, date_format(o.updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_order_process op left join bl_orders o on op.order_id=o.id left join bl_patients p on o.patient_id=p.id left join bl_doctors d on o.doctor_id=d.id left join bl_collectors c on o.collector_id=c.id where op.status='PROCESSING' and o.id='$oid' LIMIT 1";


	//$qry = "select id, order_id, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_order_process where order_id='$oid' AND status='PROCESSING' LIMIT 1";
	
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		$lab_db = null;
		if(sizeof($result) > 0){
			$order_processing_info = current($result);
			$qry = "select bl_order_process_products.id, date_format(bl_order_process_products.updated,'%b %d, %Y %H:%i:%s') as updated, p.id as product_id, p.name FROM bl_order_process_products  left join bl_products p on bl_order_process_products.product_id=p.id  where order_process_id='$order_processing_info->id'"; // Get all processed products of this order
			//echo $qry;
			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->query($qry);
			$order_processing_info->products = $stmt->fetchAll(PDO::FETCH_OBJ);
			foreach ($order_processing_info->products as $key => $value) {
				$qry="select i.id, i.name, i.description, i.unit,i.minval, i.maxval, i.currentval, concat(i.minval,' - ',i.maxval) as vals, p.name as product, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_order_process_items i left join bl_products p on i.product_id=p.id where i.product_id='".$value->product_id."' and i.order_process_product_id='".$value->id."' and i.status='ACTIVE'";
				//echo $qry;
				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}
				$stmt = $lab_db->query($qry);
				$order_processing_info->products[$key]->order_items = $stmt->fetchAll(PDO::FETCH_OBJ);
			}

			$data['data'] = $order_processing_info;
			$data['message'] = array('type'=>'success', 'msg'=>'Order Processed Successfully.');	
			echo json_encode(array_reverse($data));
		}else{
			echo '{"message" : {"type": "Error", "msg": "Order does not longer exist"}}';	
		}
	} catch(PDOException $e){
		echo '{"message" : {"type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->get('/api/order_esign/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$oid = trim($lu_ids[1]);
	$qry = "select op.id, o.id as order_id, date_format(o.order_date,'%b %d, %Y') as order_date, concat(p.title,' ',p.first_name,' ',p.last_name) as patient, concat(d.title,' ',d.first_name,' ',d.last_name) as doctor, concat(c.title,' ',c.first_name,' ',c.last_name) as collector, o.barcode,  o.observation, o.doctor_name, o.doctor_esign, op.status, date_format(o.updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_order_process op left join bl_orders o on op.order_id=o.id left join bl_patients p on o.patient_id=p.id left join bl_doctors d on o.doctor_id=d.id left join bl_collectors c on o.collector_id=c.id where op.status='PROCESSED' and o.id='$oid' LIMIT 1";


	//$qry = "select id, order_id, status, date_format(updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_order_process where order_id='$oid' AND status='PROCESSING' LIMIT 1";
	
	try{
		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		$lab_db = null;
		if(sizeof($result) > 0){
			$order_processing_info = current($result);
			$qry = "select bl_order_process_products.id, date_format(bl_order_process_products.updated,'%b %d, %Y %H:%i:%s') as updated, p.id as product_id, p.name FROM bl_order_process_products  left join bl_products p on bl_order_process_products.product_id=p.id  where order_process_id='$order_processing_info->id'"; // Get all processed products of this order
			//echo $qry;
			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->query($qry);
			$order_processing_info->products = $stmt->fetchAll(PDO::FETCH_OBJ);
			foreach ($order_processing_info->products as $key => $value) {
				$qry="select i.id, i.name, i.description, i.unit,i.minval, i.maxval, i.currentval, concat(i.minval,' - ',i.maxval) as vals, p.name as product, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_order_process_items i left join bl_products p on i.product_id=p.id where i.product_id='".$value->product_id."' and i.order_process_product_id='".$value->id."' and i.status='ACTIVE'";
				//echo $qry;
				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}
				$stmt = $lab_db->query($qry);
				$order_processing_info->products[$key]->order_items = $stmt->fetchAll(PDO::FETCH_OBJ);
			}

			$data['data'] = $order_processing_info;
			$data['message'] = array('type'=>'success', 'msg'=>'Order Retrived Successfully.');	
			echo json_encode(array_reverse($data));
		}else{
			echo '{"message" : {"type": "Error", "msg": "Order does not longer exist"}}';	
		}
	} catch(PDOException $e){
		echo '{"message" : {"type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

// item add API 
$app->put('/api/order_processing/{lids}', function($request){

	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$ordId = trim($lu_ids[1]);

	$data = $request->getParam('data');
	$status = $data['status'];

	
	$data = !empty($data['products']) ? $data['products'][0] : array();
	try{
		foreach ($data['order_items'] as $value) {

			$id 	= $value['id'];
			$currentval 	= (int)$value['currentval'];

			$qry="UPDATE bl_order_process_items SET currentval = :currentval WHERE id = :id";
			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}

			$stmt = $lab_db->prepare($qry);
			$stmt->bindParam(':currentval', $currentval, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
		}
		$opQry = "UPDATE bl_order_process set status = :status where order_id = :ord_id";
		$stmt = $lab_db->prepare($opQry);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->bindParam(':ord_id', $ordId, PDO::PARAM_STR);
		$stmt->execute();

		$oQry = "UPDATE bl_orders set status = :ord_status where id = :order_id";
		$stmt = $lab_db->prepare($oQry);
		$stmt->bindParam(':ord_status', $status, PDO::PARAM_STR);
		$stmt->bindParam(':order_id', $ordId, PDO::PARAM_STR);
		$stmt->execute();

			
		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Order Processed Successfully.');	
		echo json_encode(array_reverse($data));	

	} catch(PDOException $e){
		echo '{"message" : {"type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});

$app->put('/api/order_esign/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$ordId = trim($lu_ids[1]);

	try {
		$data = $request->getParam('data');
	
		$status = $data['status'];
		$observation = $data['observation'];
		$doctor_name = $data['doctor_name'];
		$doctor_esign = 'data:image/png;base64,'.$data['doctor_esign'];
		$sign_date = date('Y-m-d h:i:s');
		//var_dump(json_encode($data)); die;

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);

		$opQry = "UPDATE bl_order_process set status = :status where order_id = :ord_id";
		
		$stmt = $lab_db->prepare($opQry);
		$stmt->bindParam(':status', $status, PDO::PARAM_STR);
		$stmt->bindParam(':ord_id', $ordId, PDO::PARAM_STR);
		$stmt->execute();

		$oQry = "UPDATE bl_orders set observation= :observation, doctor_esign= :doctor_esign, doctor_name = :doctor_name, sign_date= :sign_dt, status = :ord_status where id = :order_id";
		$stmt = $lab_db->prepare($oQry);
		$stmt->bindParam(':observation', $observation, PDO::PARAM_STR);
		$stmt->bindParam(':doctor_esign', $doctor_esign, PDO::PARAM_STR);
		$stmt->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
		$stmt->bindParam(':sign_dt', $sign_date, PDO::PARAM_STR);
		$stmt->bindParam(':ord_status', $status, PDO::PARAM_STR);
		$stmt->bindParam(':order_id', $ordId, PDO::PARAM_STR);
		$stmt->execute();

		/*******CREATING PDF DOCUMENT********/

		if($status == 'SIGNED'){
			$html='<body>';
			$lab_db = null;
			$qry = "select op.id, o.id as order_id, date_format(o.order_date,'%b %d, %Y') as order_date, concat(p.title,' ',p.first_name,' ',p.last_name) as patient, p.address as paddr, p.city as pcity, p.mobile as pmobile, p.email_id as pemail_id, concat(d.title,' ',d.first_name,' ',d.last_name) as doctor, d.address as daddr, d.city as dcity, d.mobile as dmobile, d.email_id as demail_id, concat(c.title,' ',c.first_name,' ',c.last_name) as collector, o.barcode,  o.observation, o.doctor_name, o.doctor_esign, op.status, date_format(o.updated,'%b %d, %Y %H:%i:%s') as updated FROM bl_order_process op left join bl_orders o on op.order_id=o.id left join bl_patients p on o.patient_id=p.id left join bl_doctors d on o.doctor_id=d.id left join bl_collectors c on o.collector_id=c.id where op.status='SIGNED' and o.id='$ordId' LIMIT 1";
			$lab_db = new lab_db();
			$lab_db = $lab_db->connect($lab_id);
			if($lab_db==null) {
				throw new PDOException("Internal server error in connecting databases", 1);
			}
			$stmt = $lab_db->query($qry);
			$result = $stmt->fetchAll(PDO::FETCH_OBJ);
			$lab_db = null;
			if(sizeof($result) > 0){
				$ordInfo = current($result);
				$html = '<table style="width:100%;"><tr><td><span class="order">Order#: '.$ordId.'<span></td><td valign="right" class="dat">Date: '.$ordInfo->order_date.'</td></tr><tr><td>Name: '.$ordInfo->patient.'<div class="tip">Address: '.$ordInfo->paddr.' '.$ordInfo->pcity.'<br/>'.$ordInfo->pmobile.'</div></td><td>Ref. By: '.$ordInfo->doctor.'<div class="tip">Address: '.$ordInfo->daddr.' '.$ordInfo->dcity.'<br/>'.$ordInfo->dmobile.'</div></td></tr></table>';

				$qry = "select bl_order_process_products.id, date_format(bl_order_process_products.updated,'%b %d, %Y %H:%i:%s') as updated, p.id as product_id, p.name FROM bl_order_process_products  left join bl_products p on bl_order_process_products.product_id=p.id  where order_process_id='$ordInfo->id'"; // Get all processed products of this order
				//echo $qry;
				$lab_db = new lab_db();
				$lab_db = $lab_db->connect($lab_id);
				if($lab_db==null) {
					throw new PDOException("Internal server error in connecting databases", 1);
				}
				$stmt = $lab_db->query($qry);
				$ordInfo->products = $stmt->fetchAll(PDO::FETCH_OBJ);
				foreach ($ordInfo->products as $key => $value) {
					$html .= '<div><h3>'.$value->name.'</h3></div><table style="width:100%;"><tr class="tab_hdr"><td class="tab_hdr">Sr. #</td><td class="tab_hdr">Test Details</td><td class="tab_hdr">Adult range</td><td class="tab_hdr cval">Your\'s</td></tr>';
					$qry="select i.id, i.name, i.description, i.unit,i.minval, i.maxval, i.currentval, concat(i.minval,' - ',i.maxval) as vals, p.name as product, date_format(i.updated,'%b %d, %Y %H:%i:%s') as updated from bl_order_process_items i left join bl_products p on i.product_id=p.id where i.product_id='".$value->product_id."' and i.order_process_product_id='".$value->id."' and i.status='ACTIVE'";
					//echo $qry;
					$lab_db = new lab_db();
					$lab_db = $lab_db->connect($lab_id);
					if($lab_db==null) {
						throw new PDOException("Internal server error in connecting databases", 1);
					}
					$stmt = $lab_db->query($qry);
					$ordInfo->products[$key]->order_items = $stmt->fetchAll(PDO::FETCH_OBJ);
					$x = 0;
					foreach ($ordInfo->products[$key]->order_items as $ky => $val) {
						$x = $x + 1;
						$html .= ($val->currentval < $val->minval || $val->currentval > $val->maxval) ? '<tr class="mis"><td class="mis">'.$x.'.</td><td class="mis">'.$val->name.'</td><td class="mis">'.$val->vals.' '.$val->unit.'</td><td class="mis cval">'.$val->currentval.' '.$val->unit.'</td></tr>' : '<tr><td>'.$x.'.</td><td>'.$val->name.'</td><td>'.$val->vals.' '.$val->unit.'</td><td class="cval">'.$val->currentval.' '.$val->unit.'</td></tr>';
					}
					$html .= '</table>';
				}
				$html .= '<div class="observe">'.$ordInfo->observation.'</div><div class"foot_dr"><div class="sdiv"><h4 class="sdiv">'.$ordInfo->doctor_name.'<br/><img class="sig" src="'.$ordInfo->doctor_esign.'" /></h4><div></div>';	
			}
			$html .= '</body>';
			$pdf = new pdfDocs();
			$pdf->createOrderPDF($ordId,$html);
		}
		/********END CREATING PDF DOCUMENT************/
	
		$data['data'] = array(array('token'=>null));
		$data['message'] = array('type'=>'success', 'msg'=>'Order Signed Successfully.');	
		echo json_encode(array_reverse($data));	

	} catch(PDOException $e){
		echo '{"message" : {"type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});


