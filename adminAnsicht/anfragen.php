<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 05.01.2019
 * Time: 17:06
 */
session_start();

$userName = $_SESSION['userName'];

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}