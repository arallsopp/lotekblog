<?php
require_once('components.php');
$pageConstructor = new pageConstructor();
$obj = array();

switch($_POST['mode']){
    case 'login':
        $result = $pageConstructor->validateUser($_POST['email'],$_POST['pass']);
        if($result){
            $obj = array('validated'=>true, 'message'=>'Please wait while you are logged in');
        }else{
            $obj = array('validated'=>false, 'message'=>'Email address or password not recognised');
        }
    break;

    case 'logout':
        $pageConstructor->logoutUser();
        $obj = array('message'=>'You are now logged out');
        break;

    default:
        $obj = array('message'=>'Something went wrong');
}
echo json_encode($obj);


