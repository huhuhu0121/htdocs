<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// 데이터베이스 연결
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "shoppingmall_pj";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 현재 사용자 ID 가져오기
$user_id = $_SESSION['user_id'];

// 전체 삭제 버튼이 눌렸을 경우
if (isset($_POST['delete_all'])) {
    $sql = "CALL DeleteAllOrdersByUser(?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('모든 주문이 삭제되었습니다.');
                window.location.href = 'mypage.php'; // 주문 목록 페이지로 리디렉션
              </script>";
    } else {
        echo "오류 발생: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
