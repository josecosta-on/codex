<?php 

/**
 * Fetching tasks from a projects
 */
$app->get('/gettasks', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array(); 
    $project='0';
    $str_status='';

    if(isset($_GET['project'])){
		$project = $_GET['project'];
	}
	if(isset($_GET['status'])){
		$status = $_GET['status'];
		$str_status=" and status='$status'";
	}
   
    $result=$db->getRecords("select * from task where project='$project'$str_status");
   
    echoResponse(200, $result);
});

/**
 * Fetching create task on project
 */
$app->post('/createtask', function() use ($app) {
    $db = new DbHandler();
   
    $r = json_decode($app->request->getBody());

    $response = array();

    verifyRequiredParams(['description','project'],$r->task);
    $r->task->created_at= time()* 1000;
    $tabble_name = "task";
    $column_names = array('description','project','created_at');
    $result = $db->insertIntoTable($r->task, $column_names, $tabble_name);
    
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "task created successfully";
        $response["id"] = $result;            
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create task. Please try again";
        echoResponse(201, $response);
    }            
   
});
/**
 * Fetching update task on project
 */
$app->post('/updatetask', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());

    verifyRequiredParams(array('description','id'),$r->task);
    $r->task->end_at=time()* 1000;
    $table_name = "task";
    
    $result = $db->updateIntoTable($r->task, $table_name, 'id');
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "task update successfully";
        $response["rows"] = $result;            
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to update task. Please try again";
        echoResponse(201, $response);
    }            
   
});

/**
 * Fetching delete task on project
 */
$app->post('/deletetask', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());

    verifyRequiredParams(array('id'),$r->task);

   

    $table_name = "task";
    $column_names = array('description','user');
    
    $result = $db->deleteIntoTable($r->task, $table_name, 'id');
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "task delete successfully";
        $response["rows"] = $result;            
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to delete task. Please try again";
        echoResponse(201, $response);
    }            
   
});

?>