INSERT INTO notifications (user_id, message, is_read, created_at)
VALUES 
('Test Notification 1', 'This is a test notification description 1', NOW(), 'unread', 1),
('Test Notification 2', 'This is a test notification description 2', NOW(), 'unread', 2),
('Test Notification 3', 'This is a test notification description 3', NOW(), 'unread', 3),
('Test Notification 4', 'This is a test notification description 4', NOW(), 'read', 1),
('Test Notification 5', 'This is a test notification description 5', NOW(), 'read', 2);