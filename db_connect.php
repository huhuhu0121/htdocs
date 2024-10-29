<?php
    $servername = "localhost"; // 서버 이름 (보통 localhost)
    $username = "root"; // MySQL 사용자명
    $password = ""; // MySQL 비밀번호
    $dbname = "shoppingmall_pj"; // 데이터베이스 이름

    // MySQL 연결
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 연결 확인
    if ($conn->connect_error) {
        die("failed: " . $conn->connect_error);
    }
?>
