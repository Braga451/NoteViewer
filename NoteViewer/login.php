<?php
    require "assets/php_include/SiteFunctions.php";
    $functions = new SiteFunctions();
    if($functions->cookieVerify()){
        header("location:home.php");
    }
    if(isset($_POST["usuario"]) && isset($_POST["senha"])
    && !empty($_POST["usuario"]) && !empty($_POST["senha"])
    && strlen($_POST["usuario"]) <= 200 && strlen($_POST["senha"]) <= 200
    ){
        if($functions->loginSystem($_POST["usuario"], $_POST["senha"])){
            header("location:home.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login_register.css">
    <link rel="icon" href="assets/img/logo.png">
    <title>NoteViewer - Entrar</title>
    <!--Fonte-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet"> 
</head>
<body>
    <form method="POST" action="login.php">
        <input type="text" placeholder="NOME DE USUARIO" name="usuario" required maxlength="200">
        <input type="password" placeholder="SENHA" name="senha" required maxlength="200">
        <input type="submit" value="ENTRAR">
    </form>
</body>
</html>