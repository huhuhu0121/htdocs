<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>쇼핑몰 메인</title>      
</head>
<body>
    <?php
    session_start(); // 세션 시작

    // 로그인된 사용자의 이름이 존재하는지 확인
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        echo "<span style='float: right;'>$username 님, 환영합니다!</span>";
    }
    ?>
        <form action="result.php" method="post" style="text-align : center; float: center;  ">
            <input type="text" id="product_name" name="product_name"  id="pin" name="pin" maxlength="200" size=100" required>
            <input type="submit" value="검색">
        </form>
        <input type="button" value="로그아웃" onClick="location.href='index.html'" style="float: right;">
        <input type="button" value="장바구니" onClick="location.href='cart.html'" style="float: right;">
</body>
</html>