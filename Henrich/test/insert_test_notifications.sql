INSERT INTO notifications (user_id, message, is_read, created_at)
VALUES 
(1, 'This is a test notification description 1', 0, NOW()),
(2, 'This is a test notification description 2', 0, NOW()),
(3, 'This is a test notification description 3', 0, NOW()),
(1, 'This is a test notification description 4', 1, NOW()),
(2, 'This is a test notification description 5', 1, NOW());