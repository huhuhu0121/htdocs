<?php
session_start();
include 'db_connect.php'; // 데이터베이스 연결 파일 포함

// 로그인된 사용자인지 확인
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // 사용자의 cart_id 가져오기
    $sql_cart = "SELECT cart_id FROM Cart WHERE user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    $row_cart = $result_cart->fetch_assoc();

    // 사용자가 장바구니가 없을 경우 새로 생성
    if (!$row_cart) {
        $sql_create_cart = "INSERT INTO Cart (user_id) VALUES (?)";
        $stmt_create_cart = $conn->prepare($sql_create_cart);
        $stmt_create_cart->bind_param("i", $user_id);
        $stmt_create_cart->execute();

        $cart_id = $stmt_create_cart->insert_id;
        $stmt_create_cart->close();
    } else {
        $cart_id = $row_cart['cart_id'];
    }

    // 장바구니에 제품 추가 (같은 제품이 있을 경우 수량을 업데이트하는 기능 추가)
    $sql_cart_item = "INSERT INTO CartItem (cart_id, product_id, quantity) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE quantity = quantity + ?";
    $stmt_cart_item = $conn->prepare($sql_cart_item);
    $stmt_cart_item->bind_param("iiii", $cart_id, $product_id, $quantity, $quantity);
    
    if ($stmt_cart_item->execute()) {
        echo "장바구니에 추가되었습니다.";
    } else {
        echo "장바구니 추가 실패: " . $stmt_cart_item->error;
    }

    $stmt_cart_item->close();
    $stmt_cart->close();
} else {
    echo "로그인이 필요합니다.";
}

$conn->close();
?>
