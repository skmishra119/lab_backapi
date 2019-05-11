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
				WHERE YEAR(order_date) = YEAR(now())
				AND MONTH(order_date) = MONTH(now()) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		$result = json_decode(json_encode($result), true);
		$result = array_column($result, 'order_total', 'status');

		$graph_data = [
			isset($result["PROCESSED"]) ? (int) $result["PROCESSED"] : 0, 			
			isset($result["COMPLETED"]) ? (int) $result["COMPLETED"] : 0, 			
			isset($result["ACTIVE"]) ? (int) $result["ACTIVE"] : 0, 			
		];

		$data["order_this_month"] = [
			'order_status' => ['PROCESSED', 'COMPLETED','ACTIVE'],
			'graph_data' => $graph_data
		];


		$qry="SELECT MONTH(order_date) as month_number, count(*) as order_total, status, order_date 
		FROM bl_orders
		WHERE UNIX_TIMESTAMP(order_date) >= UNIX_TIMESTAMP(LAST_DAY(now()) - interval 3 month) 
		group by MONTH(order_date), status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		$data["order_last_three_month"] = [];

		$dataFormating = [];
		foreach ($result as $key => $value) {
			$dataFormating[$value->status][$value->month_number] = $value->order_total;
		}

		$data["order_last_three_month"] = [
			'months' => [date('M', strtotime('-2 month')), date('M', strtotime('-1 month')), date('M')],
			'graph_data' => [
				[
					'label' => 'COMPLETED',
					'data'	=> array_reverse(array_values([
						(int)date('m') => isset($dataFormating['COMPLETED'][(int)date('m')]) ? (int) $dataFormating['COMPLETED'][(int)date('m')] : 0,
						(int)date('m', strtotime('-1 month')) => isset($dataFormating['COMPLETED'][(int)date('m', strtotime('-1 month'))]) ? (int) $dataFormating['COMPLETED'][(int)date('m', strtotime('-1 month'))] : 0,
						(int)date('m', strtotime('-2 month')) => isset($dataFormating['COMPLETED'][(int)date('m', strtotime('-2 month'))]) ? (int) $dataFormating['COMPLETED'][(int)date('m', strtotime('-2 month'))] : 0,
					]))
				],

				[
					'label' => 'PROCESSED',
					'data'	=> array_reverse(array_values([
						(int)date('m') => isset($dataFormating['PROCESSED'][(int)date('m')]) ? (int) $dataFormating['PROCESSED'][(int)date('m')] : 0,
						(int)date('m', strtotime('-1 month')) => isset($dataFormating['PROCESSED'][(int)date('m', strtotime('-1 month'))]) ? (int) $dataFormating['PROCESSED'][(int)date('m', strtotime('-1 month'))] : 0,
						(int)date('m', strtotime('-2 month')) => isset($dataFormating['PROCESSED'][(int)date('m', strtotime('-2 month'))]) ? (int) $dataFormating['PROCESSED'][(int)date('m', strtotime('-2 month'))] : 0,
					]))
				],

				[
					'label' => 'ACTIVE',
					'data'	=> array_reverse(array_values([
						(int)date('m') => isset($dataFormating['ACTIVE'][(int)date('m')]) ? (int) $dataFormating['ACTIVE'][(int)date('m')] : 0,
						(int)date('m', strtotime('-1 month')) => isset($dataFormating['ACTIVE'][(int)date('m', strtotime('-1 month'))]) ? (int) $dataFormating['ACTIVE'][(int)date('m', strtotime('-1 month'))] : 0,
						(int)date('m', strtotime('-2 month')) => isset($dataFormating['ACTIVE'][(int)date('m', strtotime('-2 month'))]) ? (int) $dataFormating['ACTIVE'][(int)date('m', strtotime('-2 month'))] : 0,
					]))
				]
			]
		];


		$qry="SELECT count(*) as order_total, status FROM bl_orders
				WHERE UNIX_TIMESTAMP(order_date) >= UNIX_TIMESTAMP(now()-interval 7 DAY) group by status";

		$lab_db = new lab_db();
		$lab_db = $lab_db->connect($lab_id);
		if($lab_db==null) {
			throw new PDOException("Internal server error in connecting databases", 1);
		}
		$stmt = $lab_db->query($qry);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		$result = json_decode(json_encode($result), true);
		$result = array_column($result, 'order_total', 'status');

		$graph_data = [
			isset($result["PROCESSED"]) ? (int) $result["PROCESSED"] : 0, 			
			isset($result["COMPLETED"]) ? (int) $result["COMPLETED"] : 0, 			
			isset($result["ACTIVE"]) ? (int) $result["ACTIVE"] : 0, 			
		];

		$data["order_last_seven_month"] = [
			'order_status' => ['PROCESSED', 'COMPLETED', 'ACTIVE'],
			'graph_data' => $graph_data,
		];


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
