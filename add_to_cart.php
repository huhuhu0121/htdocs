<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// 데이터베이스 연결
$servername = "localhost";  // 서버 주소
$username_db = "root";      // DB 사용자명
$password_db = "";          // DB 비밀번호
$dbname = "shoppingmall_pj"; // 사용할 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 장바구니 추가 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_SESSION['user_id']; // 세션에서 사용자 ID를 가져온다고 가정

        // 프로시저 호출
        $stmt = $conn->prepare("CALL AddToCart(?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity); // INT 매개변수 바인딩
        $stmt->execute();

        // 성공적으로 추가된 후 리디렉션
        header("Location: cart.php"); // 장바구니 페이지로 리디렉션
        exit();
    } else {
        echo "제품 ID 또는 수량이 전달되지 않았습니다.";
    }
}

$conn->close();
?>
