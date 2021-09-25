<?php
include "DBConnection.php";
class SiteFunctions{
    private $connection;
    private $password_salt = "4n_4maz1ng_s4lt";
    public function __construct(){
        $this->connection = new DBConnection();
    }

    private function __generateToken(string $user_id): string{
        date_default_timezone_set("America/Sao_Paulo");
        $salt = "Sup3r_s3cr3t_s4lt";
        $hour = date("H:i:s:ms");
        $token = md5("{$user_id}&{$hour}&{$salt}");
        return $token;
    }

    private function __setCredentials(string $token): bool{  
        session_set_cookie_params(1440, "./", "", false, true);
        setcookie("token", $token, 0, "./", "", false, true);
        return true;
    }
    
    public function createUser(string $user_username_input, string $user_email_input, string $user_password_input): bool{
        $username_input = filter_var($user_username_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user_password = md5("{$user_password_input}&{$this->password_salt}");
        $user_email = filter_var($user_email_input, FILTER_SANITIZE_EMAIL);
        if(strlen($username_input) >= 200 || strlen($user_email) >= 200){
            return false;
        }
        $select_query = $this->connection->class_connection->prepare("SELECT ID, USERNAME FROM USERS WHERE USERNAME = :username OR EMAIL = :email");
        $select_query_params = ["username" => $username_input, "email" => $user_email];
        $select_query->execute($select_query_params);
        if($select_query->rowCount() > 0){
            return false;
        }
        else{
            if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){
                return false;
            }
            else{
                $insert_query = $this->connection->class_connection->prepare("INSERT INTO USERS VALUES(NULL, :username, :email, :user_password, 'assets/img/default_user.png', NULL)");
                $insert_query_params = ["username" => $username_input, "email" => $user_email, "user_password" => $user_password];
                $insert_query->execute($insert_query_params);
                $get_user_id_query = $this->connection->class_connection->prepare("SELECT ID FROM USERS WHERE USERNAME= :username AND EMAIL= :email");
                $get_user_id_parms = ["username" => $username_input, "email" => $user_email];
                $get_user_id_query->execute($get_user_id_parms);
                $user_id = $get_user_id_query->fetchAll()[0][0];
                $token = $this->__generateToken($user_id);
                $insert_token_query = $this->connection->class_connection->prepare("UPDATE USERS SET CURRENT_TOKEN = :token WHERE ID= :user_id");
                $insert_token_parms = ["token" => $token, "user_id" => $user_id];
                $insert_token_query->execute($insert_token_parms);
                if($this->__setCredentials($token) == false){
                    return false;
                }
                else{
                    return true;
                }
            }
        }
    }

    public function insertNote(string $note_input, string $user_token):bool{
        $note_string = filter_var($note_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(strlen($note_string) > 350){
            return false;
        }
        $token_sanitizate = filter_var($user_token, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user_id_query = $this->connection->class_connection->prepare("SELECT ID FROM USERS WHERE CURRENT_TOKEN = :token");
        $user_id_parms = ["token" => $token_sanitizate];
        $user_id_query->execute($user_id_parms);
        if($user_id_query->rowCount() == 0){
            return false;
        }
        else{
            $user_id = $user_id_query->fetchAll()[0][0];
            $date_published = date("Y/m/d");
            $insert_note_query = $this->connection->class_connection->prepare("INSERT INTO NOTES VALUES(NULL, :note_string, :data_published, :user_id)");
            $insert_note_query_parms = ["note_string" => $note_string, "data_published" => $date_published, "user_id" => $user_id];
            $insert_note_query->execute($insert_note_query_parms);
            return true;
        }
    }

    public function getNotes(string $user_id): array{
        $get_notes_query = $this->connection->class_connection->prepare("SELECT ID, CONTENT, DATA_PUBLISHED FROM NOTES WHERE USER_ID= :user_id ");
        $get_notes_parms = ["user_id" => $user_id];
        $get_notes_query->execute($get_notes_parms);
        if($get_notes_query->rowCount() == 0){
            return [];
        }
        else{
            $query_result = $get_notes_query->fetchAll();
            return $query_result;
        }
    }

    public function loginSystem(string $username_input, string $user_password_input): bool{
        $user_password = md5("{$user_password_input}&{$this->password_salt}");
        $username = filter_var($username_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(strlen($username) >= 200 || strlen($user_password) >= 200){
            return false;
        }
        $query_login_system = $this->connection->class_connection->prepare("SELECT ID, USERNAME FROM USERS WHERE USER_PASSWORD= :user_password AND USERNAME = :username");
        $query_login_system_parms = ["user_password" => $user_password, "username" => $username];
        $query_login_system->execute($query_login_system_parms);
        if($query_login_system->rowCount() == 0){
            return false;
        }
        else{
            $query_results = $query_login_system->fetchAll()[0];
            $user_id = $query_results[0];
            $new_token = $this->__generateToken($user_id);
            $update_token_query = $this->connection->class_connection->prepare("UPDATE USERS SET CURRENT_TOKEN = :token WHERE ID= :user_id");
            $update_token_parms = ["token" => $new_token, "user_id" => $user_id];
            $update_token_query->execute($update_token_parms);
            $this->__setCredentials($new_token);
            return true;
        }
    }

    public function uploadImage(): string { // Return the path of uploaded image 
        if($_FILES["imagem"]["size"] == 0){
            return "";
        }
        $verify_type_img = getimagesize($_FILES["imagem"]["tmp_name"]);
        if($_FILES["imagem"]["type"] != "image/jpg" 
        && $_FILES["imagem"]["type"] != "image/jpeg" 
        && $_FILES["imagem"]["type"] != "image/png"){
            return "";
        }
        if($verify_type_img["mime"] != "image/jpg"
        && $verify_type_img["mime"] != "image/jpeg"
        && $verify_type_img["mime"] != "image/png" ){
            return "";
        }
        $image_array = explode(".", $_FILES["imagem"]["name"]);
        $image_ext = $image_array[1];
        $hour = date("H:i:s:ms");
        $image_name = md5("{$image_array[0]}&{$hour}");
        $image_complete_name = "{$image_name}.{$image_ext}";
        if(!move_uploaded_file($_FILES["imagem"]["tmp_name"], "assets/users_profile_pic/{$image_complete_name}")){
            return "";
        }
        else{
            return "assets/users_profile_pic/{$image_complete_name}";
        }
    }

    public function alterUser(string $user_id, string $new_username_input, string $new_password_input, string $new_email_input, string $new_path_img_input): bool{
        $new_username = filter_var($new_username_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_password = md5("{$new_password_input}&{$this->password_salt}");
        $new_email = filter_var($new_email_input, FILTER_SANITIZE_EMAIL);
        $new_path_img = $new_path_img_input;
        //Test username and email
        $test_if_user_exists = $this->connection->class_connection->prepare("SELECT * FROM USERS WHERE USERNAME=:username");
        $test_if_user_exists_parms = ["username"=>$new_username];
        $test_if_user_exists->execute($test_if_user_exists_parms);
        $test_if_email_exists = $this->connection->class_connection->prepare("SELECT * FROM USERS WHERE EMAIL=:email");
        $test_if_email_exists_parms = ["email" => $new_email];
        $test_if_email_exists->execute($test_if_email_exists_parms);
        if(strlen($new_username_input) > 0 && strlen($new_username_input) < 200 && $test_if_user_exists->rowCount() == 0){
            $query_update_username = $this->connection->class_connection->prepare("UPDATE USERS SET USERNAME=:new_username WHERE ID=:user_id");
            $query_update_username_parms = ["new_username" => $new_username, "user_id" => $user_id];
            $query_update_username->execute($query_update_username_parms);
        }
        if(strlen($new_email_input) > 0 && strlen($new_email_input) < 200 
            && $test_if_email_exists->rowCount() == 0 &&filter_var($new_email_input, FILTER_VALIDATE_EMAIL))
        {
            $query_update_email = $this->connection->class_connection->prepare("UPDATE USERS SET EMAIL=:new_email WHERE ID=:user_id");
            $query_update_email_parms = ["new_email" => $new_email, "user_id" => $user_id];
            $query_update_email->execute($query_update_email_parms);
        }
        if(strlen($new_password_input) > 0 && strlen($new_password_input) < 200){
            $query_update_password = $this->connection->class_connection->prepare("UPDATE USERS SET USER_PASSWORD=:new_password WHERE ID=:user_id");
            $query_update_password_parms = ["new_password" => $new_password, "user_id" => $user_id];
            $query_update_password->execute($query_update_password_parms);
        }
        if(strlen($new_path_img) > 0){
            $query_update_path_img = $this->connection->class_connection->prepare("UPDATE USERS SET PATH_IMG=:new_path_img WHERE ID=:user_id");
            $query_update_path_img_parms = ["new_path_img" => $new_path_img, "user_id" => $user_id];
            $query_update_path_img->execute($query_update_path_img_parms);
        }
        return true;
    }

    public function deleteAccount(string $user_id): bool{
        $query_delete_user_notes = $this->connection->class_connection->prepare("DELETE FROM NOTES WHERE USER_ID=:user_id");
        $query_delete_user_notes_parms = ["user_id" => $user_id];
        $query_delete_user_notes->execute($query_delete_user_notes_parms);
        $query_delete_account = $this->connection->class_connection->prepare("DELETE FROM USERS WHERE ID=:user_id");
        $query_delete_parms = ["user_id" => $user_id];
        $query_delete_account->execute($query_delete_parms);
        return true;
    }

    public function auth(string $token) : bool{ 
        if(strlen($token) != 32){
            return false;
        }
        $token_sanitizate = filter_var($token, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query_view_token = $this->connection->class_connection->prepare("SELECT ID, USERNAME, PATH_IMG FROM USERS WHERE CURRENT_TOKEN = :token");
        $query_view_token_params = ["token" => $token_sanitizate];
        $query_view_token->execute($query_view_token_params);
        if($query_view_token->rowCount() == 0){
            return false;
        }
        else{
            $query_results = $query_view_token->fetchAll();
            session_set_cookie_params(0, "./", "", false, true);
            session_start();
            session_regenerate_id(true);
            $_SESSION["user_id"] = $query_results[0][0];
            $_SESSION["username"] = $query_results[0][1];
            $_SESSION["path_img"] = $query_results[0][2];
            return true;
        }
    }

    public function deAuth(): bool{
        try{
            setcookie("token", null, time() - 3600, "./");
            session_start();
            setcookie("PHPSESSID", null, time() - 3600, "./");
            session_destroy();
            return true;
        }
        catch(Exception $msg){
            echo "Error: ".$msg;
            return false;
        }
    }

    public function deleteNotes(string $note_id_input, string $user_id_input): bool{
        $note_id = filter_var($note_id_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user_id = filter_var($user_id_input, FILTER_SANITIZE_SPECIAL_CHARS);
        $validate_note_id_request_query = $this->connection->class_connection->prepare("SELECT ID FROM NOTES WHERE ID=:note_id AND USER_ID=:user_id");
        $validate_note_id_request_query_parms = ["note_id" => $note_id, "user_id" => $user_id];
        $validate_note_id_request_query->execute($validate_note_id_request_query_parms);
        if($validate_note_id_request_query->rowCount() == 0){
            return false;
        }
        else{
            $delete_note_query = $this->connection->class_connection->prepare("DELETE FROM NOTES WHERE ID = :note_id");
            $delete_note_query_parms = ["note_id" => $note_id];
            $delete_note_query->execute($delete_note_query_parms);
            return true;
        }
    }

    public function cookieVerify():bool{
        if(isset($_COOKIE["token"]) && !empty($_COOKIE["token"]))
            return true;
        else
            return false;
    }

    public function getUser(string $username_input): array{
        $username = filter_var($username_input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query_get_user = $this->connection->class_connection->prepare("SELECT ID, USERNAME, PATH_IMG FROM USERS WHERE USERNAME= :username");
        $query_get_user_params = ["username" => $username];
        $query_get_user->execute($query_get_user_params);
        if($query_get_user->rowCount() == 0){
            return [];
        }
        else{
            $query_get_user_result = $query_get_user->fetchAll()[0];
            return $query_get_user_result;
        }
    }
}
?>
