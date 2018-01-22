<?php

class DbHandler {

	private $conn;

	function __construct() {
		require_once 'dbConnect.php';
        // opening db connection
		$db = new dbConnect();
		$this->conn = $db->connect();
	}
    /**
     * Fetching records
     */
    public function getRecords($query) {

    	$r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        $arr = array();
        if($r->num_rows > 0) {
            while($row = $r->fetch_assoc()) {
                $arr[] = $row;  
            }
        }
    	return $arr; 
    }
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
    	$r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
    	return $result = $r->fetch_assoc();    
    }
    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {

    	$c = (array) $obj;
    	$keys = array_keys($c);
    	$columns = '';
    	$values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
        	if(!in_array($desired_key, $keys)) {
        		$$desired_key = '';
        	}else{
        		$$desired_key = $c[$desired_key];
        	}
        	$columns = $columns.$desired_key.',';
        	$values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
        	$new_row_id = $this->conn->insert_id;
        	return $new_row_id;
        } else {
        	return NULL;
        }
    }
    /**
     * Update records
     */
    public function updateIntoTable($obj, $table_name, $where) {
        
    	$where_id=$obj->{$where};
    	unset($obj->{$where});

             
    	foreach ($obj as $key => $val)
    	{
    		$valstr[] = $key." = '".$val."'";
    	}

    	$query= 'UPDATE '.$table_name.' SET '.implode(', ', $valstr).' where '.$table_name.'.'.$where.' = '.$where_id;
    	$r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        
    	if ($r) {
    		$rows = $this->conn->affected_rows;
    		return $rows;
    	} else {
    		return NULL;
    	}
    }
    /**
     * Delete records
     */
    public function deleteIntoTable($obj, $table_name, $where) {

    	$where_id=$obj->{$where};
        $query= 'DELETE FROM '.$table_name.' where '.$table_name.'.'.$where.' = '.$where_id;
    	$r = $this->conn->query($query) or die($this->conn->error.__LINE__);
    	if ($r) {
    		$rows = $this->conn->affected_rows;
    		return $rows;
    	} else {
    		return NULL;
    	}
    }
    /**
     * Fetching session
     */
    public function getSession(){
    	if (!isset($_SESSION)) {
    		session_start();
    	}
    	$sess = array();
    	if(isset($_SESSION['uid']))
    	{
    		$sess["uid"] = $_SESSION['uid'];
    		$sess["name"] = $_SESSION['name'];
            $sess["email"] = $_SESSION['email'];
    		$sess["phone"] = $_SESSION['phone'];
    	}
    	else
    	{
    		$sess["uid"] = '';
    		$sess["name"] = 'Guest';
            $sess["email"] = '';
    		$sess["phone"] = '';
    	}
    	return $sess;
    }
    /**
     * Destroy session
     */
    public function destroySession(){
    	if (!isset($_SESSION)) {
    		session_start();
    	}
    	if(isSet($_SESSION['uid']))
    	{
    		unset($_SESSION['uid']);
    		unset($_SESSION['name']);
            unset($_SESSION['email']);
    		unset($_SESSION['phone']);
    		$info='info';
    		if(isSet($_COOKIE[$info]))
    		{
    			setcookie ($info, '', time() - $cookie_time);
    		}
    		$msg="Logged Out Successfully...";
    	}
    	else
    	{
    		$msg = "Not logged in...";
    	}
    	return $msg;
    }

}

?>
