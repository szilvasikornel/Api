<?php
// include 'connect.php';
$servername = "localhost";
$username = "root";
$password = "";
$database = "realestate";

// Create connection
$dbconn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($requestMethod) {
    case "GET":
        if ($id) {
            getApartmentById($dbconn, $id);
        } else {
            getAllApartments($dbconn);
        }
        break;
    case "POST":
        createApartment($dbconn);
        break;
    case "PUT":
        if ($id) {
            updateApartment($dbconn, $id);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Hiányzik az apartman azonosítója"]);
        }
        break;
    case "DELETE":
        if ($id) {
            deleteApartment($dbconn, $id);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Hiányzik az apartman azonosítója a törléshez"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Nem támogatott HTTP típus"]);
}

// GET egy apartman ID alapján
function getApartmentById($dbconn, $id) {
    $query = "SELECT * FROM apartments WHERE id = ?";
    $stmt = $dbconn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $result->fetch_assoc()]);
    }else{
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Lakás nem található.']);    
    }
}

// GET összes apartman
function getAllApartments($dbconn) {
    $query = "SELECT * FROM apartments";
    $result = mysqli_query($dbconn, $query);

    if ($result) {
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Adatbázis hiba: " . mysqli_error($dbconn)]);
    }
}

// POST új apartman létrehozása
function createApartment($dbconn) {
    //Kliens által küldött adatok beolvasása
    $data = json_decode(file_get_contents('php://input'), true);
    //Ellenőrzi, hogy van-e bejövő adat
    if (!$data) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Hiányos adatok.']);
        return;
    };
    //SQL lekérdezés előkészítése
    $query = "INSERT INTO apartments (address, city, postal_code, size, rooms, price, owner_name, owner_phone, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    //Lekérdezés előkészítése
    $stmt = $dbconn->prepare($query);

    //Paraméterek hozzárendelése és adatok beszúrása (ssi) -> string,string,int 
    $stmt->bind_param('sssiiissss', $data['address'], $data['city'], $data['postal_code'], $data['size'], $data['rooms'],$data['price'], $data['owner_name'], $data['owner_phone'], $data['description'], $data['image_url']);

    //SQL parancs végrehajtása
    if ($stmt->execute()) {
        //Sikeres adatbeillesztés esetén válaszküldés
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Lakás sikeresen létrehozva.', 'id' => $stmt->insert_id]);
    } else {
        //Hiba esetén válaszküldés
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Hiba az adat létrehozásakor.']);
    }
}

// PUT apartman frissítése
function updateApartment($dbconn, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Hiányos adatok.']);
        return;
    }

    $query = "UPDATE apartments SET address = ?, city = ?, postal_code = ?, size = ?, rooms = ?, price = ?, owner_name = ?, owner_phone = ?, description = ?, image_url = ? WHERE id = ?";

    $stmt = $dbconn->prepare($query);

    $stmt->bind_param('sssiiissssi',$data['address'], $data['city'], $data['postal_code'], $data['size'], $data['rooms'], $data['price'], $data['owner_name'], $data['owner_phone'], $data['description'], $data['image_url'], $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Lakás sikeresen frissítve.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Hiba a frissítés során.']);
    }
}

// DELETE apartman törlése
function deleteApartment($dbconn, $id) {
    $query = "DELETE FROM apartments WHERE id = ?";
    $stmt = $dbconn->prepare($query);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        http_response_code(204);
        echo json_encode(['status' => 'success', 'message' => 'Lakás sikeresen törölve lett.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Hiba a törlés során.']);    
    }
}

mysqli_close($dbconn);
?>
