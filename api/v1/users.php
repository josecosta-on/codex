<?php 
/**
 * Fetching session user info from db
 */
$app->get('/getuser', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();

    $uid=$session['uid'];
    
    $result=$db->getOneRecord("select uid,name,email,phone from user where uid=$uid");
    
    echoResponse(200, $result);
});
/**
 * Fetching update session user
 */
$app->post('/updateuser', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();

    $response = array();
    $r = json_decode($app->request->getBody());
    $response["r"] = $session;
    verifyRequiredParams(array('name'),$r->user);

    if($r->user->uid != $session['uid']){
        $response["status"] = "error";
        $response["message"] = "Failed to update user. Please try again";
        echoResponse(201, $response);
    }   

    $name = $r->user->name;
    
    $isEq= $session['name'] == $name ? true : false ;
    

    $isExists = $db->getOneRecord("select 1 from user where name='$name'");
    if($isEq || !$isExists){

        $table_name = "user";
        
        $result = $db->updateIntoTable($r->user, $table_name, 'uid');
        // $response["r"] = $r->user;
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "user update successfully";
            $response["rows"] = $result;  
            $_SESSION['uid'] = $session['uid'];
            $_SESSION['email'] = $r->user->email;
            $_SESSION['name'] = $r->user->name;
            $_SESSION['phone'] = $r->user->phone;

            echoResponse(200, $response);
        }else if($result == 0){
            $response["status"] = "success";
            $response["message"] = "No changes ";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
          
            $response["message"] = "Failed to update user. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A user with the provided description exists!";
        echoResponse(201, $response);
    }
});

/**
 * Fetching delete session user
 */
$app->post('/deleteuser', function() use ($app) {
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