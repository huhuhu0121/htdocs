<?php
    include 'db_connect.php'; // 데이터베이스 연결 파일 포함

    // 폼에서 전송된 데이터 받기
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL 쿼리 작성
    $sql = "INSERT INTO User (username, email, password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.html'); // main.html로 리다이렉트
        exit();
    } else {
        echo "회원가입 실패: " . $conn->error;
    }

    // 데이터베이스 연결 종료
    $conn->close();
?>
