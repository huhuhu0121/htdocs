<?php
include 'db_connect.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    session_start();
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['user_id'];  // user_id 세션에 저장
    $_SESSION['username'] = $row['username'];
    header('Location: main.php');
    exit();
} else {
    echo "로그인 실패: 이메일 또는 비밀번호가 잘못되었습니다.";
}

$stmt->close();
$conn->close();
?>
