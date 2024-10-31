<?php
include 'db_connect.php'; // Include the database connection file
session_start(); // Start the session


// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get the cart_id of the user
    $sql_cart = "SELECT cart_id FROM cart WHERE user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    $row_cart = $result_cart->fetch_assoc();

    if ($row_cart) {
        $cart_id = $row_cart['cart_id'];

        // Insert the item into cartitem table
        $sql_add_item = "INSERT INTO cartitem (cart_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt_add_item = $conn->prepare($sql_add_item);
        $stmt_add_item->bind_param("iii", $cart_id, $product_id, $quantity);
        
        if ($stmt_add_item->execute()) {
            echo "장바구니에 추가되었습니다.";
        } else {
            echo "장바구니에 추가 실패: " . $stmt_add_item->error;
        }

        // Close the add item statement
        $stmt_add_item->close();
    } else {
        echo "장바구니를 찾을 수 없습니다.";
    }

    // Close the cart statement
    $stmt_cart->close();
} else {
    echo "로그인이 필요합니다.";
}

// Close database connection
$conn->close();
?>
