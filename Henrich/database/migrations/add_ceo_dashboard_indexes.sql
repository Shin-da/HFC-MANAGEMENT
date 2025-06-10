-- Indexes for Branch Performance Dashboard

-- Ensure we have an index on order_date for customerorder table
CREATE INDEX IF NOT EXISTS idx_customerorder_order_date ON customerorder(order_date);

-- Index for branch_id in customerorder for faster joins
CREATE INDEX IF NOT EXISTS idx_customerorder_branch_id ON customerorder(branch_id);

-- Combined index for branch filtering by status
CREATE INDEX IF NOT EXISTS idx_branches_status ON branches(status);

-- Index for region filtering
CREATE INDEX IF NOT EXISTS idx_branches_region ON branches(region);

-- Combined index for order filtering by status
CREATE INDEX IF NOT EXISTS idx_customerorder_status ON customerorder(status);

-- Index for location data to optimize map queries
CREATE INDEX IF NOT EXISTS idx_branches_location ON branches(latitude, longitude);

-- Combined index for date and branch for performance calculations
CREATE INDEX IF NOT EXISTS idx_customerorder_branch_date ON customerorder(branch_id, order_date);

-- Combined index for date and status for filtering completed orders
CREATE INDEX IF NOT EXISTS idx_customerorder_date_status ON customerorder(order_date, status); 