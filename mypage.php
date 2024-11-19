<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// 로그인된 사용자의 이름 및 ID 가져오기
$username = $_SESSION['username'];  // 세션에서 사용자 이름 가져오기
$user_id = $_SESSION['user_id'];    // 세션에서 사용자 ID 가져오기

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
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 목록</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* 헤더 스타일 */
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

        /* 본문 스타일 */
        .main-container {
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .order-table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .order-table th, .order-table td {
            padding: 15px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .order-table th {
            background-color: #f4f4f4;
            color: #333;
        }

        .order-table td {
            font-size: 1.1em;
        }

        .order-status {
            font-weight: bold;
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

    <div class="main-container">
    <h2>주문 목록</h2>
    <table class="order-table">
        <tr>
            <th>주문 ID</th>
            <th>제품명</th>
            <th>가격</th>
            <th>수량</th>
            <th>총 금액</th>
            <th>주문 일자</th>
            <th>주문 상태</th>
        </tr>
        <?php
        // 프로시저 호출 및 결과 가져오기
        $sql = "CALL ViewAllOrdersByUser(?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("MySQL prepare error: " . $conn->error);
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // 첫 번째 결과 세트 (주문 목록 및 주문 상태)
        if ($result = $stmt->get_result()) {
            while ($order = $result->fetch_assoc()) {
                // 주문 상태를 한글로 변환
                switch ($order['order_status']) {
                    case 'Pending':
                        $order_status = "주문 접수";
                        break;
                    case 'shipped':
                        $order_status = "배송 중";
                        break;
                    case 'completed':
                        $order_status = "배송 완료";
                        break;
                    default:
                        $order_status = "상태 불명";
                        break;
                }

                echo "<tr>
                        <td>" . $order['order_id'] . "</td>
                        <td>" . htmlspecialchars($order['product_name']) . "</td>
                        <td>" . number_format($order['price']) . " 원</td>
                        <td>" . $order['quantity'] . "</td>
                        <td>" . number_format($order['total_price']) . " 원</td>
                        <td>" . $order['created_at'] . "</td>
                        <td class='order-status'>" . htmlspecialchars($order_status) . "</td>
                        
                       
                        
                      </tr>";
            }
            
            echo "</table>";
            $result->free();
        }

        $stmt->close();
        $conn->close();
        ?>
        <form action="delete_orders.php" method="post" style="text-align: center; margin-top: 20px;">
            <button type="submit" name="delete_all" style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                전체 삭제
            </button>
        </form>
    </div>
    </table>
    </div>
</body>
</html>
