<?php
    require_once "../auth/index.php";
    use db\Database;
   
    $nome = htmlspecialchars($_POST['nome'] ?? null);
    $email = htmlspecialchars($_POST['email'] ?? null);
    $senha = htmlspecialchars($_POST['senha'] ?? null);

    try {
        
        if(!empty($nome and $email and $senha))
        {

            $db = new Database("clientes");
            $verifyEmail = $db->select("email = '$email'");
            if(!empty($verifyEmail))
            {
                array_push($response,[
                    "status" => "Warning",
                    "message" => "Este email já esta sendo usado!"
                ]);

                echo json_encode($response);
                exit;
            }

            $q = $db->insert([
                "nome" => $nome,
                "email" => $email,
                "senha" => $senha
            ]);

            array_push($response,[
                "status" => "SUCCESS",
                "message" => "O usuario foi inserido com sucesso"
            ]);

            echo json_encode($response);
            exit;
            // http_response_code(200);

        } else {
            array_push($response,[
                "status" => "Warning",
                "message" => "Preencha todos os campos"
            ]);

            echo json_encode($response);
        }

    } catch (Throwable $e) {
        echo "ERROR " . $e->getMessage();
    }
    
?>