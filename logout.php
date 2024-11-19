<?php
// 세션 시작
session_start();

// 모든 세션 데이터 삭제
session_unset();

// 세션 파기
session_destroy();

// 로그인 페이지로 리디렉션
header("Location: index.html");
exit();
?>
