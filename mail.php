<?php
error_reporting(E_ALL);
ini_set('display_errors',2);

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));



$url = array('host' => 'us-cdbr-iron-east-05.cleardb.net',
    'user' => 'bfe69f35dacc64',
    'pass' => '379b21df',
    'path' => '/heroku_f331ff3d90c27c9'
);

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);


define('DB_HOST',$server);
define('DB_USER',$username);
define('DB_PASS',$password);
define('DB_NAME',$db);


$connection = new Sql();
$connectStr = $connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$CommonFunction = new CommonFunction($connectStr);
$logs = $CommonFunction->Query("Select * from logs");
echo "<pre/>"; print_r($logs); 

die;

class CommonFunction{
	public $connectStr;
	
	public function __construct($connectStr){
		$this->connectStr = $connectStr;
	}
	
	public function getData($table,$fields=array(),$conditions=array(),$single=true){	
	
		$fieldStr = '`'.implode('`,`',$fields).'`';
		
		if(count($conditions) > 0){
			$conditionStr = '';
			//echo "<pre/>";print_r($conditions);
			foreach($conditions as $condition){	
				if(is_array($condition)){
					$conditionKey = $condition['key'];
					$conditionValue = $condition['value'];
					$conditionOp = $condition['operator'];
					$conditionStr .= "`".$conditionKey."`".$conditionOp."'".$conditionValue."'";
				} else {
					$conditionStr .= $condition;	
				}
			}						
			$sql = "Select $fieldStr from $table where $conditionStr";
		} else {
			$sql = "Select $fieldStr from $table";
		}
		$result = mysqli_query($this->connectStr, $sql);	
		$response = array();		
		if($result){		
			if(mysqli_num_rows($result) > 0){	
				$response['status'] = true;			
				if($single == true){				
					$newResponse = $result->fetch_assoc();	
				} else {			
					$newResponse = array();		
					while($row = mysqli_fetch_assoc($result)) {	
						$newResponse[] = $row;
					}
				}		
				$response['result'] = $newResponse;
			} 	else {
				$response['status'] = true;
				$response['result'] = array();
			}
		} else {	
			$response['status'] = false;			
			$response['errortype'] = 'db_error';
			$response['message'] =  $this->connectStr->error;		
		}			
		return $response; 
	}

	public function updateData($table,$fields=array(),$conditions=array()){
		//$fieldStr = '`'.implode('`,`',$fields).'`';		
		$conditionStr = '';
		//echo "<pre/>";print_r($conditions);
		//echo "<pre/>";print_r($fields); 
		foreach($conditions as $condition){						
			if(is_array($condition)){	
				$conditionKey = $condition['key'];				
				$conditionValue = $condition['value'];			
				$conditionOp = $condition['operator'];			
				$conditionStr .= "`".$conditionKey."`".$conditionOp."'".$conditionValue."'";
			} else {
				$conditionStr .= $condition;	
			}
		}
		$fieldStr = '';
		//array_pop($fields);
		foreach($fields as $field){	
			if(is_array($field)){
				$conditionKey = $field['key'];				
				$conditionValue = $field['value'];			
				$conditionOp = '=';			
				$fieldStr .= "`".$conditionKey."`".$conditionOp."'".$conditionValue."'";
			} else {
				$fieldStr .= $field;	
			}
		}
		
		
		$sql = "UPDATE $table SET $fieldStr where $conditionStr"; 	
		$result = mysqli_query($this->connectStr, $sql);	
		$response = array();		
		if($result){
			$response['status'] = true;	
			$response['result'] = 'Data is updated.';	
		} else {	
			$response['status'] = false;			
			$response['errortype'] = 'db_error';
			$response['message'] =  $this->connectStr->error;		
		}			
		return $response; 
	}
	
	public function insertData($table,$data){
		
		$array_keys = array_keys($data);
		$array_values = array_values($data);
		$queryString = "INSERT INTO `".$table."` (`".implode("`,`",$array_keys)."`) values ('".implode("','",$array_values)."')";
		$result = mysqli_query($this->connectStr, $queryString);
		echo mysqli_error($this->connectStr);
		return mysqli_insert_id($this->connectStr);
		
	}
	
	public function scalarResult($query){
		$result = mysqli_query($this->connectStr, $query);
		$row = mysqli_fetch_assoc($result);
		$scalarValue = current($row); 
		return $scalarValue;	
	}
	
	public function Query($query,$exec = true){
		$result = mysqli_query($this->connectStr, $query);	
		$response = array();
		if($exec == true){
			if($result){		
				if(mysqli_num_rows($result) > 0){	
					$response['status'] = true;							
					$newResponse = array();		
					while($row = mysqli_fetch_assoc($result)) {	
						$newResponse[] = $row;					
					}
					$response['result'] = $newResponse;		
				} 	else {
					$response['status'] = true;	
					$response['result'] = array();
				}
			} else {	
				$response['status'] = false;			
				$response['errortype'] = 'db_error';
				$response['message'] =  $this->connectStr->error;		
			}
		}		
		return $response; 
	}
	
}


?>