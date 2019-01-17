<?php

//var $api_server="http://localhost/binlab_backend/";  

//var $lab_id = '';

class db {
	private $dbhost = 'localhost';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'binlab_master_db';

	public function connect(){
		$my_con_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
		$dbCon = new PDO($my_con_str, $this->dbuser, $this->dbpass);
		$dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbCon;
	}
}

class lab_db {
	public function connect($lab_id) {
		$labCon=null;
		$db =  new db();
		$db = $db->connect();
		$LQry="select id, concat('binlab_client_',alias) as dbname, server_ip, user, pwd from lab_master where id='".$lab_id."' and status='ACTIVE'";
		try{
			$LSTM = $db->query($LQry);
			$LDTA = $LSTM->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			if(sizeof($LDTA) > 0) {
				$lab_con_str = "mysql:host={$LDTA[0]->server_ip};dbname={$LDTA[0]->dbname}";
        		$labCon = new PDO($lab_con_str, $LDTA[0]->user, $LDTA[0]->pwd);
        		$labCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			return $labCon;
		}  catch(PDOException $e) {
			echo '{"message" : {type": "Error", "msg": "'.$e->getMessage().'"}}';
		}
	}

}

class lab_config{
	public $bLab_id='';
}

class uuid_config {
	public function generate() {
    	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        	// 32 bits for "time_low"
        	mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        	// 16 bits for "time_mid"
        	mt_rand( 0, 0xffff ),

        	// 16 bits for "time_hi_and_version",
        	// four most significant bits holds version number 4
        	mt_rand( 0, 0x0fff ) | 0x4000,

        	// 16 bits, 8 bits for "clk_seq_hi_res",
        	// 8 bits for "clk_seq_low",
        	// two most significant bits holds zero and one for variant DCE1.1
        	mt_rand( 0, 0x3fff ) | 0x8000,

        	// 48 bits for "node"
        	mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    	);
	}
}
