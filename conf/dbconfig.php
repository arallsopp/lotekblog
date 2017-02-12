<?php

class databaseCredentials {
    public $address = 'localhost';
    public $user = 'root';
    public $pass = 'root';
    public $schema = 'lotek';


    function __construct(){
        switch($_SERVER['HTTP_HOST']){
            case 'localhost':
                $this->address = 'localhost';
                $this->user = 'root';
                $this->pass = 'root';
                $this->schema = 'lotek';
                break;
            default:
                echo '<pre>'; print_r($_SERVER['HTTP_HOST']); echo '</pre>';
                die('Please setup dbconfig.php to include public class `databaseCredentails`, Public strings for address, user, pass, schema.');
        }
    }
}
