<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('./dbConfig.php');

$response =  ["status" => false, "message" => "", "data" => "", "role" => ""];


if (!isset($_POST['email'])) {

    $response['status'] = false;
    $response['message'] = "Email is not present";
    echo json_encode($response);
    exit;
}

if (!isset($_POST['password'])) {

    $response['status'] = false;
    $response['message'] = "Password is not present";
    echo json_encode($response);
    exit;
}



$email = $_POST['email'];
$password = $_POST['password'];
if ($email == 'admin' && $password == 'admin') {
    $token = generateRandomString(10);


    $query = "insert into admin_tokens(token)values(?)";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$token]);

    if ($result) {
        $response['status'] = 'true';
        $response['message'] = "Login successful!";
        $response['data'] = $token;
        $response['role'] = 'admin';
        echo json_encode($response);
        exit;
    } else {
        $response['status'] = false;
        $response['message'] = "Username or Password is invalid.";
        echo json_encode($response);
        exit;
    }
}
$password = md5($password);

$query = "select id from users where email = ? and password = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$email, $password]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && $result['id']) {

    $token = generateRandomString(10);


    $query = "insert into user_tokens(token,user_id)values(?,?)";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$token, $result['id']]);

    if ($result) {
        $response['status'] = true;
        $response['message'] = "Login successful!";
        $response['data'] = $token;
        $response['role'] = 'user';
        echo json_encode($response);
        exit;
    }
} else {
    $response['status'] = false;
    $response['message'] = "Username or Password is invalid.";
    echo json_encode($response);
    exit;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
