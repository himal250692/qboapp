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


$webhook_token  = 'e300ba3d-bb27-480d-959c-701c71cfa429';
if (isset($_SERVER['HTTP_INTUIT_SIGNATURE']) && !empty($_SERVER['HTTP_INTUIT_SIGNATURE'])) {
	$payLoad = file_get_contents("php://input");
	
		$payloadHash = hash_hmac('sha256', $payLoad, $webhook_token);
        $singatureHash = bin2hex(base64_decode($_SERVER['HTTP_INTUIT_SIGNATURE']));
        
        $insertData = array();
$insertData['log'] = $singatureHash;

$CommonFunction->insertData('logs',$insertData);

        if($payloadHash == $singatureHash) {
            
        }
        
}



$payLoad = file_get_contents("php://input");

$request = json_encode($_REQUEST);




$insertData = array();
$insertData['log'] = $payLoad;
$CommonFunction->insertData('logs',$insertData);


$logs = $CommonFunction->Query("Select * from logs");
echo "<pre/>"; print_r($logs); 



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
Class Sql{	
	var $username;
	var $password;
	var $database;
	public $dbc;

	public function connect($set_host, $set_username, $set_password, $set_database)
	{
		$this->host = $set_host;
		$this->username = $set_username;
		$this->password = $set_password;
		$this->database = $set_database;

		$this->dbc = mysqli_connect($this->host, $this->username, $this->password,$this->database) or die('Error connecting to DB'); 
		return $this->dbc;
	}
	
	public function query($sql)
	{
		return mysqli_query($this->dbc, $sql) or false;
	}
	
	public function fetch($result)
	{        
		$array = mysqli_fetch_array($result);          
		return $array;
	}


	public function close()
	{
		return mysqli_close($this->dbc);
	}
	public function login($email,$password)
	{
		
		$response = array();
		if($email  == 'admin' && $password == md5('Admin@123$')){
			$response['status'] = true;
			$response['result'] = array('user_id'=>1,'name'=>'Admin');
		} else {
			$response['status'] = false;
			$response['errortype'] = 'wrong_cred';
			$response['message'] = 'User does not exist in our system.';
		}
		return $response; 
		
	}
	public function getcompany($clientId)
	{
		$sql = "Select * from qbap_company where client_id='$clientId'";
		$result = mysqli_query($this->dbc, $sql);
		$response = array();
		if($result){
			
			if(mysqli_num_rows($result) > 0){
				$response['status'] = true;
				while($row = $result->fetch_assoc()) {
					$newResponse[] = $row;
				}
				$response['result'] = $newResponse;				
			} 
			
		} else {
			$response['status'] = false;
			$response['errortype'] = 'db_error';
			$response['message'] =  $this->dbc->error;
		}
		
		return $response; 
		
	}
}

?>