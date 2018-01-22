<?php 

/**
 * Fetching session user projects
 */
$app->get('/getprojects', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
   
    $cols=['description'];    

    $user=$session['uid'];
    
    $result=$db->getRecords("select * from project where user='$user' ");
    
    echoResponse(200, $result);
});

/**
 * Create new project for session user
 */
$app->post('/createproject', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());

    verifyRequiredParams(array('description'),$r->project);

    $r->project->user = $session['uid'];

    $description = $r->project->description;
    
    $isExists = $db->getOneRecord("select 1 from project where description='$description'");
    if(!$isExists){

        $tabble_name = "project";
        $column_names = array('description','user');
        $result = $db->insertIntoTable($r->project, $column_names, $tabble_name);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "project created successfully";
            $response["id"] = $result;            
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create project. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A project with the provided description exists!";
        echoResponse(201, $response);
    }
});
/**
 * Update project for session user
 */
$app->post('/updateproject', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());

    verifyRequiredParams(array('description'),$r->project);

    if($r->project->user != $session['uid']){
        $response["status"] = "error";
        $response["message"] = "Failed to update project. Please try again";
        echoResponse(201, $response);
    }   

    $description = $r->project->description;
    
    $isExists = $db->getOneRecord("select 1 from project where description='$description'");
    if(!$isExists){

        $table_name = "project";
        $column_names = array('description','user');
        
        $result = $db->updateIntoTable($r->project, $table_name, 'id');
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "project update successfully";
            $response["rows"] = $result;            
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to update project. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A project with the provided description exists!";
        echoResponse(201, $response);
    }
});

/**
 * Delete project for session user
 */
$app->post('/deleteproject', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());

    verifyRequiredParams(array('description'),$r->project);

    if($r->project->user != $session['uid']){
        $response["status"] = "error";
        $response["message"] = "Failed to delete project. Please try again";
        echoResponse(201, $response);
    } 

    $table_name = "project";
    $column_names = array('description','user');
    
    $result = $db->deleteIntoTable((object)[
        'project'=>$r->project->id
    ], 'task', 'project');

    $result = $db->deleteIntoTable($r->project, $table_name, 'id');
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "project delete successfully";
        $response["rows"] = $result;            
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to delete project. Please try again";
        echoResponse(201, $response);
    }            
   
});

?>