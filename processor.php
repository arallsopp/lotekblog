<?php
$debug = false;
require_once('components.php');
$pageConstructor = new pageConstructor($debug);
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

    case 'imageupload':
        $obj=saveUploadedImage();
        break;

    case 'createpost':
    case 'amendpost':

        $creating = $_POST['mode'] == 'createpost';

        $conn = $pageConstructor->getConnection();

        $headline = mysqli_real_escape_string($conn,$_POST['headline']);
        $subtitle = mysqli_real_escape_string($conn,$_POST['subtitle']);
        $content  = mysqli_real_escape_string($conn,$_POST['content']);
        $date = date('Y-m-d H:i:s',strtotime($_POST['date']));

        if($creating) { //todo: make this work for editpost also
            $obj = saveUploadedImage();

            if ($obj !== null && array_key_exists('filepath', $obj)) {
                $backgroundimage = $obj['filepath'];
            } else {
                $backgroundimage = 'img/post-bg.jpg';
            }
        }
        $published = 1;

        if($creating) {
            $sql = 'INSERT INTO posts (headline,date,subtitle,userid,published)
                VALUES ("' . $headline . '", "' . $date . '", "' . $subtitle . '","' . $pageConstructor->getUserID() . '",' . $published . ')';
        }else{
            $sql = 'INSERT INTO posts (headline,date,subtitle,userid,published)
                VALUES ("' . $headline . '", "' . $date . '", "' . $subtitle . '","' . $pageConstructor->getUserID() . '",' . $published . ')';
            die('todo: you need to pass the id to this processor and you need to use it to update the query.');
        }

        if ($conn->query($sql) === TRUE) {

            //store the details
            $postid = $conn->insert_id;
            $sql = 'INSERT INTO postdetails (postid, bodycontent, backgroundimage)
                    VALUES ("' . $postid . '","' . $content . '","' . $backgroundimage . '")';

            if ($conn->query($sql) === TRUE) {
                //we are all done. No problems.
                header('location:index.php');
                $conn->close();
                exit;
            }
        }

        //if you're reaching here, there's a problem.
        $obj = array('message' => 'Error adding record: ' . $conn->error,
                     'sql'=>$sql,
                     'post' => $_POST);

        $conn->close();
        break;

    case 'deletepost':
        $conn = $pageConstructor->getConnection();

        $postid = mysqli_real_escape_string($conn,$_POST['id']);

        $sql = 'DELETE FROM posts WHERE id = "' . $postid . '"';

        if( $conn->query($sql) !== true) {
            $obj = array('message' => 'Error deleting post: ' . $conn->error,
                'sql' => $sql,
                'post' => $_POST);
        }else{
            $sql = 'DELETE FROM postdetails WHERE postid = "' . $postid . '"';

            if ($conn->query($sql) === TRUE) {
                $obj = array('message'=>'post has been deleted.');
            }else {
                $obj = array('message' => 'Error deleting post: ' . $conn->error,
                    'sql' => $sql,
                    'post' => $_POST);


            }
        }
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

function saveUploadedImage(){
    $obj=array('uploaded' => false);

    if (isset($_FILES['file']['name'])) {
        if (!$_FILES['file']['error']) {
            $name = md5(rand(100, 200));
            $ext = explode('.', $_FILES['file']['name']);
            $filename = $name . '.' . $ext[1];
            $folder = 'img/uploads/';
            if(!is_dir($folder)){
                mkdir($folder,0777,true);
            }

            $destination = $folder . $filename;
            $location = $_FILES["file"]["tmp_name"];
            move_uploaded_file($location, $destination);
            $obj = array('uploaded' => true,
                'filepath' => $destination);
        } else {
            $obj = array('uploaded' => false,
                'error' => $_FILES['file']['error']);
        }
    }
    return $obj;
}
