<?php
    require "assets/php_include/SiteFunctions.php";
    if(isset($_COOKIE) && isset($_POST["nota_texto"]) && 
    key_exists("token", $_COOKIE) && !empty($_COOKIE["token"]) && 
    !empty($_POST["nota_texto"])
    ){
        $insertNote = new SiteFunctions();
        $insertNote->insertNote($_POST["nota_texto"], $_COOKIE["token"]);
    }
    header("location:home.php");
?>