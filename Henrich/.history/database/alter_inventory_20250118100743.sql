-- Add reorder_point column if it doesn't exist
ALTER TABLE inventory 
ADD COLUMN IF NOT EXISTS reorder_point INT DEFAULT 10;

-- Update existing rows to have a default reorder_point if NULL
UPDATE inventory 
SET reorder_point = 10 
WHERE reorder_point IS NULL;

-- Add index for better performance on reorder point queries
ALTER TABLE inventory
ADD INDEX idx_reorder_point (reorder_point);

-- Optional: Set reorder points based on product category (example)
UPDATE inventory i 
JOIN productlist p ON i= p.id 
SET i.reorder_point = 
    CASE 
        WHEN p.category = 'meat' THEN 20
        WHEN p.category = 'processed' THEN 15
        ELSE 10 
    END;
