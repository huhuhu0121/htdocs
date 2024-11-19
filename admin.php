<?php
// 데이터베이스 연결 설정
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoppingmall_pj";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 프로시저 호출
$sql = "CALL GetProductOrderStatistics()";
$result = $conn->query($sql);

$productNames = [];
$totalQuantities = [];
$totalSalesAmounts = [];

// 데이터 배열에 저장
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productNames[] = $row['ProductName'];
        $totalQuantities[] = $row['TotalQuantityOrdered'];
        $totalSalesAmounts[] = $row['TotalSalesAmount'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>주문 통계</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* 탭 스타일 */
        .tab {
            overflow: hidden;
            background-color: #f1f1f1;
        }

        .tab button {
            background-color: #ccc;
            border: none;
            outline: none;
            padding: 14px 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .tab button:hover {
            background-color: #ddd;
        }

        .tab button.active {
            background-color: #4CAF50;
            color: white;
        }

        .tabcontent {
            display: none;
            padding: 20px;
        }

        .tabcontent.show {
            display: block;
        }

        /* 차트 크기 조정 */
        canvas {
            width: 50% !important;
            height: 300px !important;
        }
    </style>
</head>
<body>
    <h2>주문된 상품 통계</h2>
    
    <!-- 탭 메뉴 -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'QuantityTab')">주문 수량</button>
        <button class="tablinks" onclick="openTab(event, 'SalesTab')">판매 금액</button>
    </div>

    <!-- 탭 내용 -->
    <div id="QuantityTab" class="tabcontent">
        <h3>주문 수량 통계</h3>
        <canvas id="orderQuantityChart"></canvas>
    </div>

    <div id="SalesTab" class="tabcontent">
        <h3>판매 금액 통계</h3>
        <canvas id="orderSalesChart"></canvas>
    </div>

    <script>
        // PHP 데이터를 JavaScript로 전달
        const productNames = <?php echo json_encode($productNames); ?>;
        const totalQuantities = <?php echo json_encode($totalQuantities); ?>;
        const totalSalesAmounts = <?php echo json_encode($totalSalesAmounts); ?>;

        // 탭 기능
        function openTab(evt, tabName) {
            // 모든 탭 내용을 숨김
            let tabcontents = document.getElementsByClassName("tabcontent");
            for (let i = 0; i < tabcontents.length; i++) {
                tabcontents[i].classList.remove("show");
            }

            // 모든 탭 버튼에서 active 클래스 제거
            let tablinks = document.getElementsByClassName("tablinks");
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // 해당 탭 내용 표시 및 버튼에 active 클래스 추가
            document.getElementById(tabName).classList.add("show");
            evt.currentTarget.classList.add("active");
        }

        // 기본적으로 첫 번째 탭 활성화
        document.querySelector(".tablinks").click();

        // 차트 생성: 주문 수량 차트
        const ctxQuantity = document.getElementById('orderQuantityChart').getContext('2d');
        new Chart(ctxQuantity, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: '총 주문 수량',
                    data: totalQuantities,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 차트 생성: 판매 금액 차트
        const ctxSales = document.getElementById('orderSalesChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: '총 판매 금액',
                    data: totalSalesAmounts,
                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
