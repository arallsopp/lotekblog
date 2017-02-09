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

    case 'editinline':
        if($pageConstructor->isLoggedIn()){

            $conn = $pageConstructor->getConnection();

            $sql = 'UPDATE postdetails
                       SET ' . $_POST['column'] . ' =  "' . mysqli_real_escape_string($conn, $_POST['content']) . '"
                       WHERE postid = "' . $_POST['id'] . '"';

            if ($conn->query($sql) === TRUE) {
                $obj = array('message' => 'Record updated successfully');
            } else {
                $obj = array('message' => 'Error updating record: ' . $conn->error);
            }

            $conn->close();

        }
        break;

    case 'createpost':
        $conn = $pageConstructor->getConnection();

        $sql = 'select * from posts';

        if ($conn->query($sql) === TRUE) {
            $obj = array('message' => 'Post added');
        } else {
            $obj = array('message' => 'Error adding record: ' . $conn->error);
        }

        $conn->close();
        break;

    default:
        $obj = array('message'=>'Wasn\'t sure what you were trying to do there.',
                     'mode'=> (isset($_POST['mode'])
                               ? $_POST['mode']
                               : 'null'
                               ),
                     'post' => $_POST);
}
echo json_encode($obj);


