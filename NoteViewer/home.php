<?php
    require "assets/php_include/SiteFunctions.php";
    $functions = new SiteFunctions();
    if($functions->cookieVerify() == false){
        header("location:index.php");
    }
    else{
        if($functions->auth($_COOKIE["token"]) == false){
            if($functions->deAuth()){
                header("location:index.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Viewer - Home</title>
    <link rel="stylesheet" href="assets/css/home_user.css">
    <link rel="icon" href="assets/img/logo.png">
    <!--Fonte-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet"> 
</head>
<body>
    <div id="area_procura">
        <form method="GET" action="user.php">
            <input type="search" name="usuario">
            <input type="submit">
        </form>
    </div>
    <div id="area_perfil">
        <div id="informacao_perfil">
            <div id="config">
                <a href="config.php">
                    <img src="assets/img/settings.png">
                </a>
            </div>
            <div id="informacoes_basicas">
                <img src="<?php echo $_SESSION['path_img'];?>">
                <p>
                    <?php
                        echo filter_var($_SESSION["username"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    ?>
                </p>
            </div>
            <div></div>
        </div>
        <?php 
            //Script que carrega as notas
            $array_notas = $functions->getNotes($_SESSION["user_id"]);
            $quantidade_de_notas = sizeof($array_notas);
            for($x = 0; $x < $quantidade_de_notas; $x++){
                echo "
                <div class=\"area_nota\">
                <img src=\"{$_SESSION['path_img']}\" class=\"imagem_usuario_nota\">
                <div class=\"texto_nota\"> <!--350 Caracteres por nota-->\n".
                    filter_var($array_notas[$x][1], FILTER_SANITIZE_FULL_SPECIAL_CHARS).
                "</div>
                <form method=\"POST\" action=\"deleteNote.php\" class=\"form_deletar_nota\">
                    <input type=\"hidden\" value=\"{$array_notas[$x][0]}\" name=\"id_nota\">
                    <input type=\"submit\">
                    <img src=\"assets/img/trash.png\" class=\"deletar_nota\">
                </form>
            </div>
                ";
            }  
        ?>
    </div>
    <div id="area_adicionar_nota">
        <form method="POST" action="createNote.php">
            <textarea name="nota_texto" placeholder="Digite sua nota (maximo 350 caracteres)" maxlength="350"></textarea>
            <input type="submit" value="ADICIONAR NOTA">
        </form>
    </div>
</body>
</html>