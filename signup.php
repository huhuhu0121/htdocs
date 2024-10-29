<?php
include 'db_connect.php'; // 데이터베이스 연결 파일 포함

// 폼에서 전송된 데이터 받기
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // User 테이블에 새로운 사용자 추가
    $sql = "INSERT INTO User (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();

    // 새로 추가된 사용자의 user_id 가져오기
    $user_id = $stmt->insert_id;

    // Cart 테이블에 새로운 cart_id와 user_id 추가
    $sql = "INSERT INTO cart (user_id, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 커밋하여 트랜잭션 완료
    $conn->commit();

    // 성공 시 index.html로 리다이렉트
    header('Location: index.html');
    exit();
} catch (Exception $e) {
    // 오류가 발생하면 롤백
    $conn->rollback();
    echo "회원가입 실패: " . $e->getMessage();
}

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();
?>
