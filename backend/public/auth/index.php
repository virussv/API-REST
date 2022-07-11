<?php
    require_once "../../vendor/autoload.php";

    use db\Database;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__,3));
    $dotenv->load();

    $response = [];
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    $key = $_SERVER["KEY"];
    $keyAcess = $_SERVER['KEYacess'];

 
    //get tokens sent by header authorization
    function cathTokens(string $tokens)
    {
        //remove the identifiers
        $filter_name = str_replace("Bearer:","",$tokens);
        $filter_token = str_replace("token:","",$filter_name);
        $filter_refreshToken = str_replace("refreshToken:","",$filter_token);
        $datas = explode(",",$filter_refreshToken);

        return $datas;
    }
    //remove possible spaces
    $tokens = cathTokens($auth);
    $name = !empty($tokens[0]) ? preg_replace("/\s+/","",$tokens[0]) : "null";
    $refreshToken = !empty($tokens[1]) ? preg_replace("/\s+/","",$tokens[1]) : "null";
    $token = !empty($tokens[2]) ? preg_replace("/\s+/","",$tokens[2]) : "null";


    if($token != "null" && $name != "null" && $refreshToken != "null")
    {
        try{
            //checks if the refreshtoken is valid, if not, it falls into the catch
            $decodeRefreshToken = JWT::decode($refreshToken,new key($keyAcess,"HS256"));

                try {
                    //checks if the token is valid, if yes, it passes through authorization and a new token is sent, if not, it falls into the catch
                    $decode = JWT::decode($token,new Key($key,'HS256'));
    
                    $newToken = JWT::encode([
                        "iat" => time(),
                        "exp" => time()+60,
                        "name" => $name
                    ],$key,"HS256");
        
                    array_push($response,[
                        "token" => "valid",
                        "newToken" => $newToken
                    ]);
                    // http_response_code(200);

                } catch (Throwable $e) {
                   if($e->getMessage() === "Expired token")
                   {
                        array_push($response,[
                            "token" => "invalid"
                        ]);
            
                        array_push($response,[
                            "status" => "ERROR",
                            "message" => "the token is expired"
                        ]);
            
                        echo json_encode($response);
                        exit;
    
                   } else {
                        echo $e->getMessage();
                        exit;
                   }
                }
                
                // http_response_code(400);
    
            } catch(Throwable $e){
                if($e->getMessage() === "Expired token")
                {
                    array_push($response,[
                        "token" => "invalid"
                    ]);
    
                    array_push($response,[
                        "status" => "ERROR",
                        "message" => "acess token was expired"
                    ]);
    
                    echo json_encode($response);
                    exit;
                    // http_response_code(200);
                } else {
                    echo $e->getMessage();
                    exit;
                } 
            }
            
    } else {
        array_push($response,[
            "token" => "invalid"
        ]);

        array_push($response,[
            "status" => "ERROR",
            "message" => "the token was don't send"
        ]);

        echo json_encode($response);  
        exit;
    }


    
   
?>