<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Allow, Access-Control-Allow-Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
header("Allow: GET, POST, PUT, DELETE, OPTIONS, HEAD");
require_once 'database.php';
require_once 'jwt.php';
if($_SERVER['REQUEST_METHOD']=="OPTIONS"){
    exit();
}
$header = apache_request_headers();
$jwt = trim($header['Authorization']);
switch (JWT::verify($jwt, CONFIG::SECRET_JWT)) {
    case 1:
        header("HTTP/1.1 401 Unauthorized");
        echo "El token no es válido";
        exit();
        break;
    case 2:
        header("HTTP/1.1 408 Request Timeout");
        echo "La sesión caduco";
        exit();
        break;
}
$data = JWT::get_data($jwt, CONFIG::SECRET_JWT);
switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        if(isset($_GET['placa'])){
            $productos = new DataBase('autos');
                //campos de              campo que se   
                //base de datos          insertan el usuario
            $matricula = $_GET['placa'];
            $where = array('matricula'=>$_GET['placa']);
            $res = $productos->Read($where);
            if(empty($res)){
                header("HTTP/1.1 404 Not Found");
                $res = array("result"=>"no","message"=>"La matricula no existe en la base de datos", "matricula"=>$matricula);
                echo json_encode($res);
                break;
            }
        }else{
            $productos = new DataBase('autos');
            $res = $productos->ReadAll();
            if(empty($res)){
                header("HTTP/1.1 404 Not Found");
                $res = array("result"=>"no","message"=>"No existen autos en la base de datos");
                echo json_encode($res);
                break;
            }
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "POST":
        if(isset($_POST['placa']) && isset($_POST['marca']) && isset($_POST['modelo']) 
        && isset($_POST['color']) && isset($_POST['anio']) && isset($_POST['combustible']) && isset($_POST['renta']) ){
                //campos de              campo que se   
                //base de datos          insertan el usuario          
            $productos = new DataBase('autos');
            $datos = array(
                'matricula'=>$_POST['placa'],
                'marca'=>$_POST['marca'],
                'modelo'=>$_POST['modelo'],
                'color'=>$_POST['color'],
                'anio'=>$_POST['anio'],
                'tipo_combustible'=>$_POST['combustible'],
                'en_renta'=>$_POST['renta']
            );
            try{
                $reg = $productos->create($datos);
                $res = array("result"=>"ok","msg"=>"Se guardo el auto correctamente");
            }catch(PDOException $e){
                $res = array("result"=>"no","msg"=>$e->getMessage());    
            }
                    
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la insercion");
            header("HTTP/1.1 404 Not Found");
            echo json_encode($res);
            break;
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "PUT":
        if(isset($_GET['placa']) && isset($_GET['marca']) && isset($_GET['modelo']) 
        && isset($_GET['color']) && isset($_GET['anio']) && isset($_GET['combustible']) && isset($_GET['renta']) ){
            
            $productos = new DataBase('autos');
            $where = array('matricula'=>$_GET['placa']);
            $datos = array(
                //campos de              campo que se   
                //base de datos          insertan el usuario
                'matricula'=>$_GET['placa'],
                'marca'=>$_GET['marca'],
                'modelo'=>$_GET['modelo'],
                'color'=>$_GET['color'],
                'anio'=>$_GET['anio'],
                'tipo_combustible'=>$_GET['combustible'],
                'en_renta'=>$_GET['renta']
            );
            $reg = $productos->update($datos,$where);

            $res = array("result"=>"ok","msg"=>"Se guardo(actualizado) el auto");
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la actualizacion");
            header("HTTP/1.1 404 Not Found");
            echo json_encode($res);
            break;
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "DELETE":
        if(isset($_GET['placa'])){
            $productos = new DataBase('autos');
                //campos de              campo que se   
                //base de datos          insertan el usuario
            $matricula = $_GET['placa'];
            $where = array('matricula'=>$_GET['placa']);
            $res = $productos->Read($where);
            if(empty($res)){
                $res = array("result"=>"no","msg"=>"No se encontro esa matricula para su eliminacion");
            header("HTTP/1.1 404 Not Found");
            echo json_encode($res);
            break;
            }else{
                $where = array('matricula'=>$_GET['placa']);
            $reg = $productos->delete($where);
            $res = array("result"=>"ok","msg"=>"Se elimino el auto satisfactoriamente");
            }
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la eliminacion");
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    default:
        header("HTTP/1.1 401 Bad Request");
}