<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoppingmall_pj";

$user_username = $_POST['username'];
$user_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$user_email = $_POST['email'];
$user_phone = $_POST['phone'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CALL AddUser(?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $user_username, $user_password, $user_email, $user_phone);

if ($stmt->execute()) {
    echo "회원가입이 완료되었습니다.";
    header("Location: index.html");
} else {
    echo "회원가입에 실패했습니다: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
