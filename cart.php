<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// 로그인된 사용자의 이름과 ID 가져오기
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];  // 사용자의 ID를 세션에서 가져옴

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

// cart_item_id를 받아서 삭제 프로시저 호출
if (isset($_POST['delete_cart_item'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // 프로시저 호출
    $sql = "CALL DeleteCartItem(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_item_id); // cart_item_id를 바인딩
    $stmt->execute();
}

// 프로시저 호출 후, 장바구니 내용 재조회
$sql = "CALL ViewCartItems(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // user_id를 바인딩
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>장바구니</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #007B9E;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 1.5em;
            color: white;
            text-decoration: none;
        }
        .header-right {
            display: flex;
            align-items: center;
        }
        .welcome {
            margin: 0 15px;
            font-size: 1.2em;
        }
        .header button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s;
        }
        .header button:hover {
            background-color: #007B9E;
        }
        .cart-container {
            margin: 30px auto;
            text-align: center;
        }
        .cart-table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .cart-table th, .cart-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .cart-table th {
            background-color: #f4f4f4;
        }
        .cart-table td {
            font-size: 1.1em;
        }
        .cart-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="header">
        <a class="logo" href="main.php">쇼핑몰</a>
        <div class="header-right">
            <p class="welcome">환영합니다, <?php echo htmlspecialchars($username); ?> 님!</p>
            <form action="mypage.php" method="get">
                <button type="submit">마이페이지</button>
            </form>
            <form action="cart.php" method="get">
                <button type="submit">장바구니</button>
            </form>
            <form action="logout.php" method="post">
                <button type="submit">로그아웃</button>
            </form>
        </div>
    </div>

    <div class="cart-container">
        <h2>장바구니</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>제품명</th>
                        <th>가격</th>
                        <th>수량</th>
                        <th>합계</th>
                        <th>삭제</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_price = 0;
                    while ($row = $result->fetch_assoc()) {
                        $product_name = htmlspecialchars($row['product_name']);
                        $price = $row['price'];
                        $quantity = $row['quantity'];
                        $total = $price * $quantity;
                        $total_price += $total;
                        echo "<tr>";
                        echo "<td>{$product_name}</td>";
                        echo "<td>" . number_format($price) . " 원</td>";
                        echo "<td>{$quantity}</td>";
                        echo "<td>" . number_format($total) . " 원</td>";
                        echo "<td>
                                <form method='post' action='cart.php'>
                                    <input type='hidden' name='cart_item_id' value='{$row['cart_item_id']}'>
                                    <button type='submit' name='delete_cart_item'>X</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="cart-footer">
                <p><strong>총합: <?php echo number_format($total_price); ?> 원</strong></p>
                <form action="checkout.php" method="post">
                    <button type="submit">주문하기</button>
                </form>
            </div>
        <?php else: ?>
            <p>장바구니에 제품이 없습니다.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// 데이터베이스 연결 종료
$conn->close();
?>
