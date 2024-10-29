<?php
include 'db_connect.php'; // 데이터베이스 연결 파일 포함

$product_name = $_POST['product_name']; // POST 방식으로 받은 제품 이름

// SQL 쿼리 작성 (제품 이름을 안전하게 처리하기 위해 prepared statements 사용)
$sql = "SELECT * FROM product WHERE product_name = ?"; // '?'로 바인딩하여 SQL 인젝션 방지
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_name); // "s"는 문자열 타입
$stmt->execute();
$result = $stmt->get_result();

echo "<h1 onClick=\"location.href='main.html'\" style=\"cursor:pointer;\">쇼핑몰</h1>";

if ($result->num_rows > 0) {
    // 제품이 존재하면 정보 출력
    while ($row = $result->fetch_assoc()) {
        echo "제품 이름: " . htmlspecialchars($row['product_name']) . "<br>";
        echo "설명: " . htmlspecialchars($row['description']) . "<br>";
        echo "가격: " . htmlspecialchars($row['price']) . "원<br>";
        echo "재고: " . htmlspecialchars($row['stock']) . "개<br>";
    }
} else {
    // 제품이 존재하지 않으면 메시지 출력
    echo "제품을 찾을 수 없습니다.";
}

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();
?>
