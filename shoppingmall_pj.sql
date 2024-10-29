drop schema if exists shoppingmall_pj;
create schema shoppingmall_pj;
use shoppingmall_pj;

-- User 테이블: 회원 정보 관리
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Category 테이블: 상품 카테고리 정보
CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

-- Product 테이블: 상품 정보 관리
CREATE TABLE Product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Category(category_id)
);

-- Cart 테이블: 사용자의 장바구니 관리
CREATE TABLE Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,  -- UNIQUE 제약 조건을 추가하여 하나의 사용자가 하나의 장바구니만 가지도록 제한
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- CartItem 테이블: 장바구니에 담긴 상품
CREATE TABLE CartItem (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES Cart(cart_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

-- Order 테이블: 주문 정보 관리
CREATE TABLE `Order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- OrderItem 테이블: 주문에 포함된 상품
CREATE TABLE OrderItem (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `Order`(order_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

-- Review 테이블: 상품 리뷰 관리
CREATE TABLE Review (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT CHECK(rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Product(product_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);


-- User 테이블에 데이터 삽입 (한국 사용자)
INSERT INTO User (username, password, email, phone) VALUES
('김민수', 'password123', 'minsoo.kim@example.com', '010-1234-5678'),
('이하나', 'password456', 'hana.lee@example.com', '010-2345-6789'),
('박지수', 'password789', 'jisoo.park@example.com', '010-3456-7890'),
('최준호', 'password101', 'junho.choi@example.com', '010-4567-8901'),
('정예은', 'password202', 'yeun.jung@example.com', '010-5678-9012');

-- Category 테이블에 데이터 삽입 (카테고리: 옷, 음식, 도구)
INSERT INTO Category (category_name) VALUES
('옷'),
('음식'),
('도구');

-- Product 테이블에 데이터 삽입 (상품: 옷, 음식, 도구)
INSERT INTO Product (product_name, description, price, stock, category_id) VALUES
('반팔 티셔츠', '여름용 시원한 반팔 티셔츠', 15000, 100, 1),  -- 옷 카테고리
('청바지', '데일리로 입기 좋은 청바지', 40000, 50, 1),         -- 옷 카테고리
('김치', '신선한 배추김치 1kg', 10000, 200, 2),               -- 음식 카테고리
('떡볶이', '매콤달콤한 떡볶이 500g', 5000, 300, 2),           -- 음식 카테고리
('전기 드릴', '다목적으로 사용 가능한 전기 드릴', 75000, 30, 3), -- 도구 카테고리
('망치', '튼튼한 철제 망치', 12000, 80, 3);                   -- 도구 카테고리

-- Cart 테이블에 데이터 삽입 (사용자 장바구니)
INSERT INTO Cart (user_id) VALUES
(1), (2), (3), (4), (5);

-- CartItem 테이블에 데이터 삽입 (장바구니에 담긴 상품)
INSERT INTO CartItem (cart_id, product_id, quantity) VALUES
(1, 1, 2),  -- 김민수의 장바구니에 반팔 티셔츠 2개
(2, 3, 1),  -- 이하나의 장바구니에 김치 1개
(3, 5, 1),  -- 박지수의 장바구니에 전기 드릴 1개
(4, 4, 3),  -- 최준호의 장바구니에 떡볶이 3개
(5, 2, 1);  -- 정예은의 장바구니에 청바지 1개

-- Order 테이블에 데이터 삽입 (주문 정보)
INSERT INTO `Order` (user_id, total_price, order_status) VALUES
(1, 30000, 'Pending'),  -- 김민수의 주문
(2, 10000, 'Shipped'),  -- 이하나의 주문
(3, 75000, 'Pending'),  -- 박지수의 주문
(4, 15000, 'Completed'),  -- 최준호의 주문
(5, 40000, 'Pending');  -- 정예은의 주문

-- OrderItem 테이블에 데이터 삽입 (주문에 포함된 상품)
INSERT INTO OrderItem (order_id, product_id, quantity, price) VALUES
(1, 1, 2, 15000),  -- 김민수의 주문: 반팔 티셔츠 2개
(2, 3, 1, 10000),  -- 이하나의 주문: 김치 1개
(3, 5, 1, 75000),  -- 박지수의 주문: 전기 드릴 1개
(4, 4, 3, 5000),   -- 최준호의 주문: 떡볶이 3개
(5, 2, 1, 40000);  -- 정예은의 주문: 청바지 1개

-- Review 테이블에 데이터 삽입 (상품 리뷰)
INSERT INTO Review (product_id, user_id, rating, comment) VALUES
(1, 1, 5, '정말 시원하고 좋아요!'),
(3, 2, 4, '김치 맛이 매우 좋습니다.'),
(5, 3, 5, '전기 드릴 성능이 훌륭합니다.'),
(4, 4, 3, '맛있지만 약간 매웠어요.'),
(2, 5, 4, '청바지가 편하고 튼튼해요.');

-- 김민수의 장바구니 목록을 확인하는 SELECT 문
SELECT 
    u.username AS 사용자명,
    p.product_name AS 상품명,
    p.description AS 상품설명,
    p.price AS 가격,
    ci.quantity AS 수량,
    (p.price * ci.quantity) AS 총가격
FROM 
    Cart c
JOIN 
    User u ON c.user_id = u.user_id
JOIN 
    CartItem ci ON c.cart_id = ci.cart_id
JOIN 
    Product p ON ci.product_id = p.product_id
WHERE 
    u.username = '김민수';

-- 김민수의 주문 목록를 확인하는 SELECT문
SELECT 
    u.username AS 사용자명,
    o.order_id AS 주문번호,
    o.total_price AS 총가격,
    o.order_status AS 주문상태,
    p.product_name AS 상품명,
    oi.quantity AS 수량,
    (oi.price * oi.quantity) AS 상품별가격
FROM 
    `Order` o
JOIN 
    User u ON o.user_id = u.user_id
JOIN 
    OrderItem oi ON o.order_id = oi.order_id
JOIN 
    Product p ON oi.product_id = p.product_id
WHERE 
    u.username = '김민수'; 

-- 특정 카테고리의 재고를 확인하는 SELECT문
SELECT 
    c.category_name AS 카테고리명,
    p.product_name AS 상품명,
    p.price AS 가격,
    p.stock AS 재고수량
FROM 
    Product p
JOIN 
    Category c ON p.category_id = c.category_id
WHERE 
    c.category_name = '옷';

-- 상품별 리뷰와 평점을 확인하는 SELECT 문

SELECT 
    p.product_name AS 상품명,
    u.username AS 사용자명,
    r.rating AS 평점,
    r.comment AS 리뷰,
    AVG(r.rating) OVER (PARTITION BY r.product_id) AS 평균평점
FROM 
    Review r
JOIN 
    Product p ON r.product_id = p.product_id
JOIN 
    User u ON r.user_id = u.user_id
ORDER BY 
    p.product_name, r.created_at;

select * from User;