-- SQL Script to Drop Redundant approved_account Table

-- IMPORTANT: Ensure you have a database backup and have verified
-- that no essential, unique data remains ONLY in this table.

USE dbhenrichfoodcorps;

DROP TABLE IF EXISTS `approved_account`;

SELECT 'Table approved_account dropped successfully.' AS message; 