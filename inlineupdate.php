<?php
if (!isset($_SESSION)) session_start();

ini_set('display_errors','on');
error_reporting(E_ALL);


$allowEdit = (isset($_POST['pass']) && $_POST['pass'] == 'alphab3ta');

if($allowEdit) {


    $conn = new mysqli("localhost", "root", "root", "lotek");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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