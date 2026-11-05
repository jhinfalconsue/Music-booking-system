-- TEST DATA: generated sample data for local development and demos.
-- This file creates realistic synthetic records to exercise the schema and satisfy performance testing needs.

USE musician_booking_db;

INSERT INTO users (id, full_name, email, phone, password_hash, role, status, created_at, updated_at)
VALUES
('USR000001', 'Admin User', 'admin@musivanta.local', '5550000001', '$2y$10$M2I0a0JWjVFRS4vDQ2rKjuyA9R0e0N5w9f5wP1n1r4i0q7U8Xk6A2', 'admin', 'active', NOW(), NOW());

WITH RECURSIVE seq AS (
    SELECT 2 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 121
)
INSERT INTO users (id, full_name, email, phone, password_hash, role, status, created_at, updated_at)
SELECT
    CONCAT('USR', LPAD(n, 6, '0')),
    CONCAT('User ', n),
    CONCAT('user', n, '@musivanta.local'),
    CONCAT('555', LPAD(n + 100000, 8, '0')),
    '$2y$10$abcdefghijklmnopqrstuv',
    CASE WHEN n % 3 = 0 THEN 'musician' ELSE 'customer' END,
    'active',
    NOW(),
    NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 120
)
INSERT INTO musicians (id, user_id, stage_name, bio, experience_years, hourly_rate, city, state, country, profile_image, is_verified, created_at, updated_at)
SELECT
    CONCAT('MUS', LPAD(n, 6, '0')),
    CONCAT('USR', LPAD(n + 1, 6, '0')),
    CONCAT('Artist ', n),
    CONCAT('Professional musician and performer #', n, ' specializing in live entertainment for events and celebrations.'),
    (n % 12) + 2,
    150 + (n * 7.5),
    CASE n % 6
        WHEN 1 THEN 'New York' WHEN 2 THEN 'Chicago' WHEN 3 THEN 'Los Angeles' WHEN 4 THEN 'Miami' WHEN 5 THEN 'Austin' ELSE 'Seattle' END,
    CASE n % 5
        WHEN 1 THEN 'NY' WHEN 2 THEN 'IL' WHEN 3 THEN 'CA' WHEN 4 THEN 'FL' ELSE 'WA' END,
    'USA',
    NULL,
    CASE WHEN n % 5 = 0 THEN 1 ELSE 0 END,
    NOW(),
    NOW()
FROM seq;

INSERT INTO genres (id, name, created_at, updated_at) VALUES
('GEN000001', 'Jazz', NOW(), NOW()),
('GEN000002', 'Rock', NOW(), NOW()),
('GEN000003', 'Classical', NOW(), NOW()),
('GEN000004', 'Pop', NOW(), NOW()),
('GEN000005', 'Acoustic', NOW(), NOW()),
('GEN000006', 'DJ', NOW(), NOW()),
('GEN000007', 'Soul', NOW(), NOW()),
('GEN000008', 'Afrobeat', NOW(), NOW());

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 120
), pairs AS (
    SELECT 1 AS pair_id
    UNION ALL
    SELECT pair_id + 1 FROM pairs WHERE pair_id < 2
)
INSERT INTO musicians_genres (musician_id, genre_id)
SELECT
    CONCAT('MUS', LPAD(seq.n, 6, '0')),
    CONCAT('GEN', LPAD(((seq.n + pairs.pair_id) % 8) + 1, 6, '0'))
FROM seq
CROSS JOIN pairs;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 120
)
INSERT INTO services (id, musician_id, name, description, price, created_at, updated_at)
SELECT
    CONCAT('SRV', LPAD(n, 6, '0')),
    CONCAT('MUS', LPAD(n, 6, '0')),
    CONCAT('Service ', n),
    CONCAT('Custom event performance package for #', n),
    120 + (n * 8.5),
    NOW(),
    NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 120
)
INSERT INTO availability (id, musician_id, day_of_week, start_time, end_time, is_available, created_at, updated_at)
SELECT
    CONCAT('AVL', LPAD(n, 6, '0')),
    CONCAT('MUS', LPAD(n, 6, '0')),
    CASE n % 7
        WHEN 1 THEN 'Monday' WHEN 2 THEN 'Tuesday' WHEN 3 THEN 'Wednesday' WHEN 4 THEN 'Thursday' WHEN 5 THEN 'Friday' WHEN 6 THEN 'Saturday' ELSE 'Sunday' END,
    TIME_FORMAT(SEC_TO_TIME(3600 + (n * 59)), '%H:%i:%s'),
    TIME_FORMAT(SEC_TO_TIME(7200 + (n * 59)), '%H:%i:%s'),
    1,
    NOW(),
    NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 250
)
INSERT INTO bookings (id, customer_id, musician_id, service_id, booking_date, start_time, end_time, status, notes, created_at, updated_at)
SELECT
    CONCAT('BKG', LPAD(n, 6, '0')),
    CONCAT('USR', LPAD((n % 120) + 2, 6, '0')),
    CONCAT('MUS', LPAD((n % 120) + 1, 6, '0')),
    CONCAT('SRV', LPAD((n % 120) + 1, 6, '0')),
    DATE_ADD('2026-01-01', INTERVAL n DAY),
    '18:00:00',
    '21:00:00',
    CASE n % 4
        WHEN 0 THEN 'completed'
        WHEN 1 THEN 'accepted'
        WHEN 2 THEN 'pending'
        ELSE 'cancelled'
    END,
    CONCAT('Test booking note ', n),
    NOW(),
    NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 250
)
INSERT INTO payments (id, booking_id, user_id, amount, payment_method, status, paid_at, created_at, updated_at)
SELECT
    CONCAT('PAY', LPAD(n, 6, '0')),
    CONCAT('BKG', LPAD(n, 6, '0')),
    CONCAT('USR', LPAD((n % 120) + 2, 6, '0')),
    100 + (n * 2.25),
    CASE n % 3 WHEN 0 THEN 'card' WHEN 1 THEN 'bank_transfer' ELSE 'wallet' END,
    'paid',
    NOW(),
    NOW(),
    NOW()
FROM seq;

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 200
)
INSERT INTO notifications (id, user_id, title, message, is_read, created_at, updated_at)
SELECT
    CONCAT('NOT', LPAD(n, 6, '0')),
    CONCAT('USR', LPAD((n % 120) + 2, 6, '0')),
    CONCAT('Test Alert ', n),
    CONCAT('This is a generated test notification for record #', n, '.'),
    CASE WHEN n % 3 = 0 THEN 1 ELSE 0 END,
    NOW(),
    NOW()
FROM seq;
