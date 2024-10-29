<?php
    include 'db_connect.php'; // 데이터베이스 연결 파일 포함

    // 폼에서 전송된 데이터 받기
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 이메일과 비밀번호가 일치하는 사용자 조회
    $sql = "SELECT * FROM User WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 세션을 시작하고 사용자 정보를 저장할 수 있음
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['email'] = $email; // 사용자 세션에 이메일 저장
        $_SESSION['username'] = $row['username'];
        // 홈 페이지로 리다이렉트
        header('Location: main.php'); // main.html로 리다이렉트
        exit(); // 스크립트 종료
    } else {
        // 사용자가 없으면 로그인 실패
        echo "로그인 실패: 이메일 또는 비밀번호가 잘못되었습니다.";
    }
    

    // 데이터베이스 연결 종료
    $conn->close();
?>
