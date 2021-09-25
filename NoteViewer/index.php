<?php
    require "assets/php_include/SiteFunctions.php";
    $cookie_function = new SiteFunctions();
    if($cookie_function->cookieVerify()){
        header("location:home.php");
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="icon" href="assets/img/logo.png">
    <title>NoteViewer</title>
    <!--Fonte-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet"> 
</head>
<body>
    <div id="img">
        <img src="assets/img/logo.png">
    </div>
    <div id="login_register_area">
            <div class="registro_login">
                <a href="register.php"> CRIAR CONTA</a>
            </div>
            <div class="registro_login">
                <a href="login.php">ENTRAR</a>
            </div>
    </div>
</body>
</html>
