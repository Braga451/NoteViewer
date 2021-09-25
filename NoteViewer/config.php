<?php
    require "assets/php_include/SiteFunctions.php";
    session_start();
    $functions = new SiteFunctions();
    if($functions->cookieVerify() == false){
        header("location:index.php");
    }
    if(isset($_POST["opcao"])){
        $opcao_form = $_POST["opcao"];
        switch($opcao_form){
            case "VOLTAR":
                header("location:home.php");
                break;
            case "SAIR":
                $functions->deAuth();
                header("location:index.php");
                break;
            case "DELETAR":
                $functions->deleteAccount($_SESSION["user_id"]);
                $functions->deAuth();
                header("location:index.php");
                break;
            case "ALTERAR":
                $new_user_name = "";
                $new_password = "";
                $new_email = "";
                $new_path_img = "";
                if(isset($_POST["nome_de_usuario"]) && strlen($_POST["nome_de_usuario"]) <= 200){
                    $new_user_name = filter_var($_POST["nome_de_usuario"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                if(isset($_FILES["imagem"]) && $_FILES["imagem"]["size"] > 0 && !empty($_FILES["imagem"]["name"])){
                    $new_path_img = $functions->uploadImage();
                }
                if(isset($_POST["senha"]) && strlen($_POST["senha"]) <= 200){
                    $new_password = $_POST["senha"];
                }
                if(isset($_POST["email"]) && strlen($_POST["email"]) <= 200 && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                    $new_email = $_POST["email"];
                }
                $functions->alterUser($_SESSION["user_id"], $new_user_name, $new_password, $new_email, $new_path_img);
                header("location:home.php");
                break;
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/config.css">
    <link rel="icon" href="assets/img/logo.png">
    <title>NoteViewer - Configurar</title>
    <!--Fonte-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet"> 
</head>
<body>
    <div id="area_config">
        <form method="POST" action="config.php" id="voltar_deletar">
            <input type="submit" name="opcao" value="VOLTAR">
            <input type="submit" name="opcao" value="DELETAR">
        </form>
        <form method="POST" action="config.php" id="area_formulario_principal" enctype="multipart/form-data">
            <img src="assets/img/default_user.png" id="default_user_img">
            <label for="image_upload">
                <img src="assets/img/camera.png" id="camera_icon">
            </label>
            <input type="file" accept="image/*" id="image_upload" name="imagem">
            <div id="formulario_itens">
                <input type="text" placeholder="NOME DE USUARIO" name="nome_de_usuario" maxlength="250">
                <input type="password" placeholder="SENHA" name="senha" maxlength="250">
                <input type="email" placeholder="EMAIL" name="email">
                <input type="submit" value="ALTERAR" name="opcao">
            </div>
        </form>
        <form method="POST" action="config.php" id="sair">
            <input type="submit" name="opcao" value="SAIR">
        </form>
    </div>
</body>
</html>
