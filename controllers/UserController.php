<?php
require_once '../models/UserModel.php';
require_once '../config/global.php';

switch ($_GET['op']) {
    case 'insertarUsuario':
        //-----------------------------------------------------------------------------
        $contrasena=(isset($_POST['contrasena']) ? trim($_POST['contrasena']) : "");
        //ENCRIPTAR
        $pepper = PAPER;//variable global
        $contrasena_peppered = hash_hmac("sha256", $contrasena, $pepper);
        $contrasena_hashed = password_hash($contrasena_peppered, PASSWORD_ARGON2ID);
        //-----------------------------------------------------------------------------

        $usuario = new User();
        $usuario->setIdRol(isset($_POST['tipoUsuario']) ? trim($_POST['tipoUsuario']): 0); //INT VAL PARA PARSEARLOS A INT
        $usuario->setIdProfesion(isset($_POST['profesion']) ? trim($_POST['profesion']) : 0);
        $usuario->setCedula(isset($_POST['cedula']) ? trim($_POST['cedula']) : "");
        $usuario->setNombre(isset($_POST['nombre']) ? trim($_POST['nombre']) : "");
        $usuario->setApellido1(isset($_POST['apellido1']) ? trim($_POST['apellido1']) : "");
        $usuario->setApellido2(isset($_POST['apellido2']) ? trim($_POST['apellido2']) : "");
        $usuario->setEdad(isset($_POST['edad']) ? trim($_POST['edad']) : 0);
        $usuario->setDireccion(isset($_POST['direccion']) ? trim($_POST['direccion']) : "");
        $usuario->setTelefono(isset($_POST['telefono']) ? trim($_POST['telefono']) : 0);
        $usuario->setEmail(isset($_POST['email']) ? trim($_POST['email']) : "");
        //$usuario->setContrasena(isset($_POST['contrasena']) ? trim($_POST['contrasena']) : "");
        $usuario->setContrasena($contrasena_hashed);
        $usuario->setImagenUrl(isset($_POST['imagen']) ? trim($_POST['imagen']) : ""); // Usamos la URL de la imagen
    
        if ($usuario->insertarUsuario() == true) { //true es la respuesta del modelo al insertar la cita

            $response = array();
            $response[] = [
                "status" => true,
                "message" => "Usuario insertado exitosamente"
            ];
            echo json_encode($response);
        } else {
            $response = array();
            $response[] = [
                "status" => false,
                "message" => "Error al insertar un usuario"
            ];
            echo json_encode($response);
        }
        break;

    case 'iniciarSesion':
        $usuario = new User();
        $vEmail = isset($_POST['email']) ? trim($_POST['email']) : "";
        $vContrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : "";

        //----------------------------------------------------------------------------
        $pepper = PAPER;
        $vContrasena_peppered = hash_hmac("sha256", $vContrasena, $pepper);
        $vContrasena_hashed = $usuario->obtenerContrasena($vEmail);
        
        if (password_verify($vContrasena_peppered, $vContrasena_hashed)) {
            $usuario->setEmail($vEmail);

            if ($usuario->iniciarSesion2()) {
                $response = [
                    "status" => true,
                    "message" => "Sesión iniciada",
                    "nombre" => $_SESSION['usuario']['nombre'] ,
                    "nombreRol" => $_SESSION['usuario']['nombreRol']
                ];
            } else {
                $response = [
                    "status" => false,
                    "message" => "Error al obtener los datos del usuario"
                ];
            }
        
            echo json_encode($response);
            exit;
        }
        else {
            $response = [
                "status" => false,
                "message" => "Error al iniciar sesión"
            ];
            echo json_encode($response);
        }

        //-----------------------------------------------------------------------------
        //$usuario->setEmail(isset($_POST['email']) ? trim($_POST['email']) : "");
        //$usuario->setContrasena(isset($_POST['contrasena']) ? trim($_POST['contrasena']) : "");
        /*
        if ($usuario->iniciarSesion2($usuario->getEmail(), $usuario->getContrasena()) == true) {

            $response = array();
            $response[] = [
                "status" => true,
                "message" => "Sesión iniciada",
                "nombre" => $_SESSION['usuario']['nombre'],
                "nombreRol" => $_SESSION['usuario']['nombreRol'] //revisar si eliminar
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = array();
            $response[] = [
                "status" => false,
                "message" => "Error al iniciar sesión"
            ];
            echo json_encode($response);
        }*/
        break;

    case 'obtenerProfesiones':
        //actaulmete esta sin uso
        $usuario = new User();
        $res = $usuario->obtenerProfesiones();

        $response = [
            "status" => true,
            "message" => "Profesiones obtenidas",
            "datos" => $res
        ];
    
        echo json_encode($response); // Asegúrate de codificar como JSON correctamente
        break;

    case 'insertarRedes':
        $usuario = new User();

        $idUsuario = $_SESSION['usuario']['idUsuario'];
 
            $usuario-> setId($idUsuario); 
            $usuario->setInstagram(isset($_POST['instagram']) ? trim($_POST['instagram']) : 0);
            $usuario->setFacebook(isset($_POST['facebook']) ? trim($_POST['facebook']) : 0);

            //var_dump($idUsuario);

        if ($usuario->insertarRedes() == true) { //true es la respuesta del modelo al insertar la cita

            $response = array();
            $response[] = [
                "status" => true,
                "message" => "Redes  insertadas exitosamente"
            ];
            echo json_encode($response);
        } else {
            $response = array();
            $response[] = [
                "status" => false,
                "message" => "Error al insertar las Redes"
            ];
            echo json_encode($response);
        }
        break;
    case 'editar':     
        $id = isset($_SESSION['usuario']['idUsuario']) ? $_SESSION['usuario']['idUsuario'] : null;
        if ($id === null) {
            echo 2; // Retorna error si no se encuentra el ID en la sesión
            break;
        }
    
        $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
        $direccion = isset($_POST["direccion"]) ? trim($_POST["direccion"]) : "";
        $telefono = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : "";
        $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
        $instagram = isset($_POST["instagram"]) ? trim($_POST["instagram"]) : "";
        $facebook = isset($_POST["facebook"]) ? trim($_POST["facebook"]) : "";
        $cedula = isset($_POST["cedula"]) ? trim($_POST["cedula"]) : "";
        $imagen_url = isset($_POST["imagen"]) ? trim($_POST["imagen"]) : "";
        $profesion = isset($_POST["profesion"]) ? trim($_POST["profesion"]) : "";
    
        $usuario = new User();
        $usuario->setId($id);
        $usuario->setNombre($nombre);
        $usuario->setDireccion($direccion);
        $usuario->setTelefono($telefono);
        $usuario->setEmail($email);
        $usuario->setInstagram($instagram);
        $usuario->setFacebook($facebook);
        $usuario->setCedula($cedula);
        $usuario->setImagenUrl($imagen_url);
        $usuario->setIdProfesion($profesion);
    
        $modificados = $usuario->modificarUsuario();
        if ($modificados > 0) {
            
            echo 1;
        } else {
            echo 0;
        }
        break;
        
    case 'obtenerUsuario':
        $usuario = new User();
        $res = $usuario->obtenerUsuario($_GET['id']);
        
        if($res){
            $response = array();
            $response = [
                "status" => true,
                "message" => "Usuario obtenido",
                "usuario" => $res
            ];

            echo json_encode($response);
        }else{
            $response = array();
            $response = [
                "status" => false,
                "message" => "Error al obtener el usuario"
            ];
            echo json_encode($response);
        }
        break;
    
    case 'listarUsuarios':
        $usuario = new User();
        $res = $usuario->listarUsuarios();

        $response = [
            "status" => true,
            "message" => "Usuarios obtenidos",
            "datos" => $res
        ];
    
        echo json_encode($response); // Asegúrate de codificar como JSON correctamente
        break;
    
    case 'actualizarVariablesSesion':
        // Obtener y limpiar el ID del usuario
        $id = isset($_POST['idUsuario']) ? trim($_POST['idUsuario']) : "";
    
        // Registrar el ID recibido en los logs
        error_log("ID recibido en actualizarVariablesSesion: " . var_export($id, true));
    
        // Validar si el ID es válido antes de continuar
        if (empty($id)) {
            error_log("Error: ID de usuario vacío o no recibido.");
            echo json_encode([
                "status" => false,
                "message" => " actVSession id usuario no valido"
            ]);
            break;
        }
    
        // Instanciar el modelo de usuario y actualizar sesión
        $usuario = new User();
        $res = $usuario->actualizarVariablesSesion($id);
    
        // Respuesta JSON basada en el resultado
        echo json_encode([
            "status" => $res ? true : false,
            "message" => $res ? "Variables actualizadas" : "Error al actualizar las variables de sesión"
        ]);
        break;
        
        

    case 'enviarEmailContrasena':
                /*
                $emailObtenido = isset($_GET['email']) ? trim($_GET['email']) : "";
        
        
                // Validar email
                if (empty($emailObtenido) || !filter_var($emailObtenido, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(["status" => "error", "message" => "Correo inválido"]);
                    exit;
                }
        
                $api_key = "re_8ffVGDs3_2Ru4aefJSSwTYb5MVWm3ZCAL";
        
                $data = [
                    "from" => "Workly <onboarding@resend.dev>",
                    "to" => [$emailObtenido],
                    "subject" => "Restablece tu contraseña",
                    "html" => "
                    <h1 style='color: #4CAF50;'>¡Hola!</h1>
                    <p>Parece que olvidaste tu contraseña. No te preocupes, ¡estamos aquí para ayudarte!</p>
                    <p>Haz clic en el botón de abajo para restablecer tu contraseña:</p>
                    <form action='localhost/SC502_3C2024_MN_G1/views/recuperarContrasena.php' method='POST'>
                        <input type='hidden' name='email' value='" . urlencode($emailObtenido) . "'>
                        <button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Restablecer contraseña</button>
                    </form>
                    <br />
                    <p>Si no solicitaste restablecer tu contraseña, ignora este correo.</p>
                    <p>Atentamente,</p>
                    <p>El equipo de soporte de Workly.</p>
                "
                ];
        
                $ch = curl_init("https://api.resend.com/emails");
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer $api_key",
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
        
                if ($http_code === 200) {
                    echo json_encode(["status" => "success", "message" => "Correo enviado exitosamente"]);
                } else {
                    echo json_encode(["status" => "error", "message" => $response]);
                }
        
                break;*/
}
