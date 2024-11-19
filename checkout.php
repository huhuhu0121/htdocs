<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// 로그인된 사용자의 ID 가져오기
$user_id = $_SESSION['user_id'];

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

// 주문 생성 버튼이 클릭되었을 때 프로시저 호출
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // 프로시저 호출
    $sql = "CALL CreateOrder(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // user_id를 바인딩
    $stmt->execute();

    // 주문 생성이 성공적으로 완료되었으면 팝업 메시지를 띄움
    echo "<script type='text/javascript'>
            alert('주문 완료');
            window.location.href = 'main.php'; // 주문 완료 후 메인 페이지로 리디렉션
          </script>";
}

// 장바구니 항목 조회
$sql = "SELECT ci.cart_item_id, p.product_name, p.price, ci.quantity
        FROM CartItem ci
        JOIN Product p ON ci.product_id = p.product_id
        WHERE ci.cart_id = (SELECT cart_id FROM Cart WHERE user_id = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// 데이터베이스 연결 종료
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문하기</title>
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        .checkout-container {
            margin: 50px auto;
            text-align: center;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            border-radius: 8px;
            width: 60%;
        }
        .checkout-container h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .checkout-container button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .checkout-container button:hover {
            background-color: #218838;
        }
        .cart-summary {
            margin-bottom: 30px;
            text-align: left;
        }
        .cart-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-summary th, .cart-summary td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        .cart-summary th {
            background-color: #f4f4f4;
        }
        .cart-summary .total {
            font-weight: bold;
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="header">
        <a class="logo" href="main.php">쇼핑몰</a>
        <div class="header-right">
            <p class="welcome">환영합니다, <?php echo htmlspecialchars($_SESSION['username']); ?> 님!</p>
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

    <div class="checkout-container">
        <h2>선택하신 항목을 주문하시겠습니까?</h2>
        
        <?php if (count($cart_items) > 0): ?>
        <div class="cart-summary">
            <table>
                <thead>
                    <tr>
                        <th>제품명</th>
                        <th>가격</th>
                        <th>수량</th>
                        <th>합계</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo number_format($item['price']); ?> 원</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'] * $item['quantity']); ?> 원</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td colspan="3">총합</td>
                        <td><?php echo number_format($total_price); ?> 원</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <form action="checkout.php" method="post">
            <button type="submit" name="checkout">주문하기</button>
        </form>
        <?php else: ?>
            <p>장바구니에 제품이 없습니다.</p>
        <?php endif; ?>
    </div>
</body>
</html>
