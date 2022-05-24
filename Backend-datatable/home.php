<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
require("./pages/metodi.php");
$page=@$_GET["page"] ?? 0;
$size=@$_GET["size"] ?? 10;
$method = $_SERVER['REQUEST_METHOD']; 
$totalElements = get_totalElements();
$totPages = get_totPages($totalElements, $length);
$url = "http://localhost:8080/employees/index.php";

$modulo = array(
  "data" => array(),
  "recordsFiltrati" => intval($totalElements), 
  "recordsTotali" => intval($totalElements) 
);

        switch($method){
                
            case 'POST':
                $modulo["data"] = getpage($page, $size);
                echo json_encode($modulo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                break;

            case 'DELITE':
              deleteEmployee($_GET['id']);
              echo json_encode($modulo);
              break;
            case 'PUT':
              $data = json_decode(file_get_contents('php://input'), true);
              editEmployee($_GET['id'], $data["birth_date"], $data['first_name'], $data['last_name'], $data['gender']);
              echo json_encode($data);
              break;
            default:
              header("HTTP/1.1 400 BAD REQUEST");
              break;
        }

        function get_totalElements()
        {
            require ("./pages/connessione.php");
    
            $query = "SELECT count(*) FROM employees";
    
            $result = $mysqli-> query($query);
            $totE = $result-> fetch_row();
    
            return $totE[0];
        }
    
        
        function get_totPages($totalElements, $size)
        {
            require ("./pages/connessione.php");
    
            $totP = ceil($totalElements/$size) -1;
            return $totP;
        }
    
        function href($url, $page, $size){
            return $url . "?page=" . $page . "&size=" . $size;
        }
    
        function set_link($page, $size, $totPages, $url)
        {
            $links = array(
                "first" => array ( "href" => href($url, 0, $size)),
                "self" => array ( "href" => href($url, $page, $size), "templated" => true),
                "last" => array ( "href" => href($url, $totPages, $size))
            );
            
            if($page > 0){
                $links["prev"] = array( "href" => href($url, $page - 1, $size));
            }
            
            if($page < $totPages){
                $links["next"] = array ( "href" => href($url, $page + 1, $size));
            }
            
            return $links;
        }
?>
