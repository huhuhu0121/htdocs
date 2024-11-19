<?php
// 세션 시작
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoppingmall_pj";

// 사용자가 입력한 이메일과 비밀번호
$user_email = $_POST['email'];
$user_password = $_POST['password'];

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 관리자 로그인 처리
if ($user_email === 'admin') {
    // 관리자 로그인 성공: 세션에 관리자 정보 저장
    $_SESSION['email'] = 'admin';
    $_SESSION['username'] = '관리자';
    echo "<script>alert('관리자 로그인 성공!'); window.location.href = 'admin.php';</script>";
    exit();
}

// 이메일에 해당하는 사용자 정보를 가져오기 위한 쿼리
$sql = "SELECT user_id, password, username FROM User WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // 비밀번호가 맞는지 확인
    $stmt->bind_result($user_id, $hashed_password, $username);
    $stmt->fetch();

    if (password_verify($user_password, $hashed_password)) {
        // 로그인 성공: 세션에 사용자 정보 저장
        $_SESSION['email'] = $user_email;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;  // user_id 세션에 저장

        // 알림 메시지와 리디렉션
        echo "<script>alert('로그인 성공! 환영합니다, $username 님!'); window.location.href = 'main.php';</script>";
        exit();
    } else {
        // 비밀번호 불일치
        echo "<script>alert('비밀번호가 틀렸습니다.'); window.location.href = 'index.html';</script>";
        exit();
    }
} else {
    // 이메일 없음
    echo "<script>alert('해당 이메일이 존재하지 않습니다.'); window.location.href = 'index.html';</script>";
    exit();
}

// 리소스 해제
$stmt->close();
$conn->close();
?>
