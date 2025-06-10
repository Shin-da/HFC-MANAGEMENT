# Improved Database Schema for Henrich Food Corps

## Entity-Relationship Diagram (Text-Based)

```
+---------------+          +----------------+         +---------------+
|    users      |          | customeraccount|         |   products    |
+---------------+          +----------------+         +---------------+
| user_id (PK)  |          | customerid (PK)|         |productcode(PK)|
| username      |          | firstname      |         | productname   |
| usermail      |          | lastname       |         | category      |
| password      |          | email          |         | description   |
| first_name    |          | phone          |         | unit_price    |
| last_name     |          | address        |         | weight        |
| role          |<---------| created_by     |         | created_at    |
| status        |          | created_at     |         | updated_at    |
| created_at    |          | updated_at     |         | last_online   |
| updated_at    |          +----------------+         | is_online     |
| last_online   |                 |                          |
| is_online     |                 |                          |
+---------------+                 |                          |
       |                          |                          |
       |                          v                          |
       |                 +----------------+                  |
       |                 | customerdetails|                  |
       |                 +----------------+                  |
       |                 | id (PK)        |                  |
       |                 | customerid (FK)|                  |
       |                 | address        |                  |
       |                 | city           |                  |
       |                 | country        |                  |
       |                 | postal_code    |                  |
       |                 +----------------+                  |
       |                                                     |
       |                                                     |
       v                                                     v
+---------------+          +----------------+         +---------------+
| user_history  |          | customerorder  |         |   inventory   |
+---------------+          +----------------+         +---------------+
| history_id(PK)|          | orderid (PK)   |         | id (PK)       |
| user_id (FK)  |          | customerid (FK)|-------->| productcode(FK)|
| action        |          | orderdate      |         | availablequan |
| previous_status|         | status         |         | reorderpoint  |
| new_status    |          | ordertotal     |         | last_updated  |
| modified_by(FK)|         | paymentmethod  |         +---------------+
| timestamp     |          | branch_id (FK) |                |
+---------------+          +----------------+                |
                                   |                         |
                                   |                         |
                                   v                         |
                          +----------------+                 |
                          |    orderlog    |                 |
                          +----------------+                 |
                          | id (PK)        |                 |
                          | orderid (FK)   |-----------------+
                          | productcode(FK)|
                          | quantity       |
                          | unit_price     |
                          | subtotal       |
                          | orderdate      |
                          +----------------+
                                   |
                                   |
                                   v
                          +----------------+        +----------------+
                          |   branches     |        |branch_inventory|
                          +----------------+        +----------------+
                          | branch_id (PK) |------->| id (PK)        |
                          | branch_name    |        | branch_id (FK) |
                          | branch_location|        | productcode(FK)|
                          | branch_manager |        | available_qty  |
                          | contact_number |        | reorder_point  |
                          | email          |        | last_updated   |
                          | status         |        +----------------+
                          | created_at     |
                          | updated_at     |
                          +----------------+
                                   |
                                   |
                                   v
                          +----------------+        +----------------+
                          | stockmovement  |        |stockactivitylog|
                          +----------------+        +----------------+
                          | id (PK)        |        | id (PK)        |
                          | productcode(FK)|        | activity_type  |
                          | quantity       |        | product_id (FK)|
                          | movementtype   |        | quantity       |
                          | reason         |        | user_id (FK)   |
                          | reference      |        | timestamp      |
                          | user_id (FK)   |        | details        |
                          | timestamp      |        +----------------+
                          +----------------+
```

## Key Relationships

1. **User Management**:
   - `users` is the central table for all user accounts
   - `user_history` tracks all changes to user accounts

2. **Customer Management**:
   - `customeraccount` stores basic customer information
   - `customerdetails` contains additional customer details
   - Foreign key from `customeraccount` to `users` (for created_by)

3. **Order Processing**:
   - `customerorder` contains order header information
   - `orderlog` contains order line items (products ordered)
   - Foreign keys connect orders to customers, branches and products

4. **Inventory Management**:
   - `products` stores product master data
   - `inventory` tracks stock levels
   - `branch_inventory` tracks inventory at specific branches
   - `stockmovement` records all stock changes
   - `stockactivitylog` provides audit trail for inventory changes

5. **Branch Management**:
   - `branches` stores information about company locations
   - Connected to orders and inventory through relationships

## Views for Reporting

1. `vw_sales_by_period`: Aggregates sales data by time periods
2. `vw_product_performance`: Shows product sales performance metrics
3. `vw_branch_performance`: Analyzes branch performance metrics
4. `vw_inventory_value`: Calculates current inventory value
5. `vw_monthly_performance`: Shows monthly business performance

## Notes on Improvements

- All tables now have proper primary keys
- Foreign key constraints ensure data integrity
- Consolidated redundant user-related tables
- Proper relationships between customer orders and inventory
- Tracking mechanisms for inventory movements and user actions 