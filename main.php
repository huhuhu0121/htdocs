<?php
// 세션 시작
session_start();

// 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

// 로그인된 사용자의 이름 가져오기
$username = $_SESSION['username'];

// 데이터베이스 연결
$servername = "localhost";  // 서버 주소
$username_db = "root";      // DB 사용자명
$password_db = "";          // DB 비밀번호
$dbname = "shoppingmall_pj";    // 사용할 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 프로시저 호출
$sql = "CALL GetProducts()"; // GetProducts 프로시저 호출
$result = $conn->query($sql);

// 쿼리 실행 결과 확인
if (!$result) {
    // 쿼리가 실패한 경우 오류 메시지 출력
    die("쿼리 실행 오류: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>메인 페이지</title>
    <style>
        /* CSS 스타일은 동일 */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between; /* 좌우 정렬 */
            align-items: center;
            padding: 20px;
            background-color: #007B9E; 
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            font-size: 1.5em; /* 폰트 크기 */
            color: white; /* 글씨 색상 */
            text-decoration: none; /* 밑줄 제거 */
        }
        .header-right {
            display: flex;
            align-items: center; /* 수직 중앙 정렬 */
        }
        .welcome {
            margin: 0 15px; /* 오른쪽 여백 추가 */
            font-size: 1.2em;
        }
        .header button {
            background-color: #0056b3; 
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px; /* 버튼 사이 여백 */
            transition: background-color 0.3s;
        }
        .header button:hover {
            background-color: #007B9E; 
        }
        .search-container {
            margin: 30px auto;
            text-align: center;
        }
        .search-container input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .search-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #0056b3; 
            color: white;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s;
        }
        .search-container button:hover {
            background-color: #007B9E; 
        }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 30px auto;
            gap: 20px;
        }
        .product {
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: white;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-name {
            font-size: 1.2em;
            margin-top: 10px;
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

    <div class="search-container">
        <form action="search.php" method="get">
            <input type="text" name="query" placeholder="검색어를 입력하세요" required>
            <button type="submit">검색</button>
        </form>
    </div>

   <!-- 제품 목록 섹션 -->
   <div class="product-list">
    <?php
    if ($result && $result->num_rows > 0) {
        // 각 제품을 출력
        while($row = $result->fetch_assoc()) {
            $productName = htmlspecialchars($row['product_name']);
            echo '<a href="search.php?query=' . urlencode($productName) . '" style="text-decoration: none; color: inherit;">'; // 제품 클릭 시 이동할 링크
            echo '<div class="product">';
            echo '<div class="product-name">' . htmlspecialchars($row['product_name']) . '</div>';
            echo '<div class="product-price">' . number_format(round($row['price'])) . ' 원</div>';
            // 이미지 출력이 필요하면 여기에 추가
            // echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Product Image">';
            echo '</div>'; // 제품 div 종료
        }
    } else {
        echo "제품이 없습니다.";
    }
    $conn->close();
    ?>
</div>


</body>
</html>
