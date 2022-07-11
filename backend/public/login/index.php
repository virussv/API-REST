<?php
    require_once "../../vendor/autoload.php";
    use db\Database;
    use Firebase\JWT\JWT;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__,3));
    $dotenv->load();

    $db = new Database("usersAPI");

    $nome = htmlspecialchars($_POST['nome'] ?? null);
    $senha = htmlspecialchars($_POST['senha'] ?? null);

    $verify = $db->select("nome = '$nome'","nome,senha");

    //simple login validation
    if(!empty($verify) && password_verify($senha,$verify->senha)){
        //check if the user is banned
        $ban = $db->select("nome = '$nome'","ban");
        if($ban->ban == "1")
        {
            die("Você está banido!");
        }

        //token
        $payload = [
            "iat" => time(),
            "exp" => time()+60,
            "name" => "$nome"
        ];

        //refresh token
        $refreshToken = [
            "iat" => time(),
            "exp" => time()+1800,
        ];

        $token = JWT::encode($payload,$_ENV['KEY'],'HS256');
        $refreshToken = JWT::encode($refreshToken,$_ENV['KEYacess'],'HS256');

        $datas = [
            "name" => $nome,
            "refreshToken" => $refreshToken,
            "token" => $token
        ];

        echo json_encode($datas);
        
    } else {
        http_response_code(401);
        die("Usuario ou senha não cadastrados");
    }

?>