<?php
session_start();
include 'db_connect.php'; // 데이터베이스 연결 파일 포함

$product_name = $_POST['product_name']; // POST 방식으로 받은 제품 이름

// 제품을 검색하는 쿼리
$sql = "SELECT * FROM Product WHERE product_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_name);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1 onClick=\"location.href='main.html'\" style=\"cursor:pointer;\">쇼핑몰</h1>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "제품 이름: " . htmlspecialchars($row['product_name']) . "<br>";
        echo "설명: " . htmlspecialchars($row['description']) . "<br>";
        echo "가격: " . htmlspecialchars($row['price']) . "원<br>";
        echo "재고: " . htmlspecialchars($row['stock']) . "개<br>";

        // 장바구니에 추가하는 폼
        echo "<form action='add_to_cart.php' method='post'>";
        echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'>";
        echo "<label for='quantity'>수량: </label>";
        echo "<input type='number' name='quantity' value='1' min='1' max='" . htmlspecialchars($row['stock']) . "' required>";
        echo "<input type='submit' value='장바구니 담기'>";
        echo "</form><br>";
    }
} else {
    echo "제품을 찾을 수 없습니다.";
}

$stmt->close();
$conn->close();
?>
