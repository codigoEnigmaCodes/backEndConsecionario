<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Allow, Access-Control-Allow-Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
header("Allow: GET, POST, PUT, DELETE, OPTIONS, HEAD");
require_once 'database.php';
require_once 'jwt.php';
//sin errores
if($_SERVER['REQUEST_METHOD']=='GET'){
    if(isset($_GET['user']) && isset($_GET['password'])){
        $u = $_GET['user'];
        $p = $_GET['password'];
        $users = new DataBase('users');
                            //donde llegan //nombre en bd
        $resultado = $users->read( array('usuario'=>$u, 'contrasena'=>$p) );
        if($resultado){
            $token = JWT::create(array('user'=>$u), Config::SECRET_JWT);
            $respuesta = array('auth'=>'si', 'token'=>$token, 'message'=>'This token expires in 4 hours');
        }else{
            $respuesta = array('auth'=>'no', 'token'=>'error to generate token');
        }
        echo json_encode($respuesta);
    }else{
        header("HTTP/1.1 401 Bad Request");
    }
}else{
    header("HTTP/1.1 401 Bad Request");
}