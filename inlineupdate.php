<?php
require_once('components.php');
$pageConstructor = new pageConstructor();

if($pageConstructor->isLoggedIn()){

    $conn = $pageConstructor->getConnection();

    $sql = 'UPDATE postdetails
           SET ' . $_POST['column'] . ' =  "' . mysqli_real_escape_string($conn, $_POST['content']) . '"
           WHERE postid = "' . $_POST['id'] . '"';

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();

}