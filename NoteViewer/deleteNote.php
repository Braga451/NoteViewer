<?php
    session_start();
    if(isset($_POST["id_nota"]) && !empty($_POST["id_nota"])
    && isset($_SESSION) && key_exists("user_id", $_SESSION)
    ){ 
        require "assets/php_include/SiteFunctions.php";
        $deleteNote = new SiteFunctions();
        if($deleteNote->deleteNotes($_POST["id_nota"], $_SESSION["user_id"])){
            header("location:home.php");
        }
        else{
            header("location:index.php");
        }
    }
    else{
        header("location:index.php");
    }
?>