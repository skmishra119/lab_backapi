<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods:  *");
header("Content-Type: application/json");

global $lab_id;
const VALID_REPORTS = array("weekly_report", "monthly_report", "weekly_ratio", "monthly_ratio");

$app->get('/api/reports/{reptype}/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);
	$reptype = $request->getAttribute('reptype');

	if(!in_array($reptype, VALID_REPORTS))
		throw new Exception("Invalid report type request", 1);
	try{
		if($reptype === "weekly_report"){
			$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE YEAR(order_date) = YEAR(now() - INTERVAL 1 MONTH)
				AND MONTH(order_date) = MONTH(now() - INTERVAL 1 MONTH) group by status";


			// $qry="select count(id) as order_total, MONTH(order_date) as month, YEAR(order_date) as year from bl_orders group by YEAR(order_date), MONTH(order_date)";
		}else if($reptype === "monthly_report"){
			$qry="SELECT count(*) as order_total, status FROM bl_orders

				WHERE YEAR(order_date) = YEAR(now() - INTERVAL 1 MONTH)
				AND MONTH(order_date) = MONTH(now() - INTERVAL 1 MONTH) group by status";
		}

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

$app->get('/api/reports/{lids}', function($request){
	$lu_ids = explode('::',$request->getAttribute('lids'));
	$lab_id = trim($lu_ids[0]);
	$uid = trim($lu_ids[1]);


	try{
		$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE YEAR(order_date) = YEAR(now() - INTERVAL 1 MONTH)
				AND MONTH(order_date) = MONTH(now() - INTERVAL 1 MONTH) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data["order_this_month"] = $stmt->fetchAll(PDO::FETCH_OBJ);


		$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE UNIX_TIMESTAMP(order_date) >= UNIX_TIMESTAMP(now()-interval 3 month) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data["order_last_three_month"] = $stmt->fetchAll(PDO::FETCH_OBJ);


		$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE UNIX_TIMESTAMP(order_date) >= UNIX_TIMESTAMP(now()-interval 3 month) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data["order_last_seven_month"] = $stmt->fetchAll(PDO::FETCH_OBJ);


		$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE YEAR(order_date) = YEAR(now() - INTERVAL 1 MONTH)
				AND MONTH(order_date) = MONTH(now() - INTERVAL 1 MONTH) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$data["order_this_month_daily"] = $stmt->fetchAll(PDO::FETCH_OBJ);


		$lab_db = null;
		echo json_encode($data);
	} catch(PDOException $e){
		echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
	}
});
