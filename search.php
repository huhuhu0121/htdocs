    <?php
    // 세션 시작
    session_start();

    // 사용자가 로그인하지 않은 경우, 로그인 페이지로 리디렉션
    if (!isset($_SESSION['username'])) {
        header("Location: index.html");
        exit();
    }

    // 데이터베이스 연결
    $host = 'localhost'; // 데이터베이스 호스트
    $db = 'shoppingmall_pj'; // 데이터베이스 이름
    $user = 'root'; // 데이터베이스 사용자 이름
    $password = ''; // 데이터베이스 비밀번호

    $conn = new mysqli($host, $user, $password, $db);

    // 연결 오류 체크
    if ($conn->connect_error) {
        die("연결 실패: " . $conn->connect_error);
    }

    // 검색어 가져오기
    $search_query = $_GET['query'];

    // 프로시저 호출
    $stmt = $conn->prepare("CALL SearchProducts(?)");
    $stmt->bind_param("s", $search_query);
    $stmt->execute();

    // 결과 가져오기
    $result = $stmt->get_result();

    $username = $_SESSION['username']; // 로그인된 사용자의 이름 가져오기
    ?>

    <!DOCTYPE html>
    <html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>쇼핑몰</title>
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
            .search-results {
                margin: 20px auto;
                max-width: 1000px;
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #007B9E;
                color: white;
            }
            td input[type="number"] {
                padding: 5px;
                width: 60px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <a class="logo" href="main.php">쇼핑몰</a> <!-- 쇼핑몰 링크 추가 -->
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

        <div class="search-results">
            <h1>검색 결과</h1>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>상품 이름</th>
                        <th>설명</th>
                        <th>가격</th>
                        <th>재고</th>
                        <th>카테고리</th>
                        <th>수량</th>
                        <th></th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo number_format($row['price'], 0) . "원"; ?></td>
                            <td><?php echo htmlspecialchars($row['stock']) . "개"; ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($row['stock']); ?>" required>
                                    <button type="submit">장바구니 추가</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>검색 결과가 없습니다.</p>
            <?php endif; ?>
        </div>

    </body>
    </html>

    <?php
    // 연결 종료
    $stmt->close();
    $conn->close();
    ?>
