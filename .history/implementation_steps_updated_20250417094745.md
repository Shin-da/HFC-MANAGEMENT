# Implementation Steps for Database Restructuring through phpMyAdmin (Updated)

Dahil may nagkaroon ng error sa original scripts, gumawa tayo ng mga fixed versions. Gamit ang mga fixed scripts na ito, sundin ang sumusunod na steps:

## Step 1: Backup ng Database

1. Buksan ang phpMyAdmin sa browser (http://localhost/phpmyadmin/)
2. I-click ang database "dbhenrichfoodcorps" sa left sidebar
3. I-click ang "Export" tab sa top menu
4. Piliin ang "Custom" option para sa export method
5. Siguraduhing naka-check ang "Add DROP TABLE" at "Add CREATE TABLE"
6. I-click ang "Go" button para i-download ang backup SQL file

## Step 2: Check ang Email Column Name sa Users Table

1. Sa phpMyAdmin, i-click ang database "dbhenrichfoodcorps"
2. I-click ang table "users" sa left sidebar
3. I-click ang "Structure" tab
4. Hanapin ang column name na ginagamit para sa email (posibleng 'usermail', 'email', o 'useremail')
5. Take note ng exact column name na ito para reference

## Step 3: Execute ang Fixed Data Integrity Check

1. Sa phpMyAdmin, i-click ang database "dbhenrichfoodcorps"
2. I-click ang "SQL" tab
3. Buksan ang file `data_integrity_check_fixed.sql` gamit ang text editor
4. I-copy at i-paste ang contents nito sa SQL query textarea
5. I-click ang "Go" button para ma-execute
6. I-check ang results para ma-identify ang mga data issues

## Step 4: Ayusin ang Data Issues

1. Sa phpMyAdmin, i-click ang database "dbhenrichfoodcorps"
2. I-click ang "SQL" tab
3. Buksan ang file `data_cleanup_fixed.sql` gamit ang text editor
4. I-copy at i-paste ang contents nito sa SQL query textarea
5. I-click ang "Go" button para ma-execute
6. I-check kung may error messages at i-fix kung meron

## Step 5: Implement ang Schema Changes (By Section)

Para maiwasan ang mga error, i-implement natin ang schema changes section by section:

### 5.1 Identify Email Column at Backup
1. Buksan ang `db_schema_fixes_fixed.sql` sa text editor
2. I-copy ang section na "IDENTIFY EMAIL COLUMN NAME" at "BACKUP STATEMENTS" (lines 3-45) sa SQL textarea ng phpMyAdmin
3. I-execute ito
4. Verify na naka-detect nito ang tamang email column name

### 5.2 Fix User Tables
1. I-copy ang section na "FIX USER TABLES" (lines 47-117) sa SQL textarea ng phpMyAdmin
2. I-execute ito
3. I-check kung may error messages at ayusin kung meron

### 5.3 Fix Customer Tables
1. I-copy ang section na "FIX CUSTOMER TABLES" (lines 119-130) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 5.4 Fix Order Tables
1. I-copy ang section na "FIX ORDER TABLES" (lines 132-155) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 5.5 Fix Inventory Tables
1. I-copy ang section na "FIX INVENTORY TABLES" (lines 157-179) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 5.6 Add Indexes and Views
1. I-copy ang section na "ADD INDEXES FOR PERFORMANCE" at "CREATE NEW VIEWS FOR REPORTING" (lines 191-238) sa SQL textarea ng phpMyAdmin
2. I-execute ito

## Step 6: Verify ang Changes

1. Suriin ang structure ng mga tables sa phpMyAdmin:
   - Check ang mga foreign key relationships (table structure > relations view)
   - Verify ang mga indexes na na-create
   - Check ang mga views na na-create

2. Run test queries para ma-validate ang functionality:
   ```sql
   -- Test query para sa relations
   SELECT co.orderid, co.orderdate, ca.firstname, ca.lastname, 
          ol.productcode, p.productname, ol.quantity, ol.unit_price
   FROM customerorder co
   JOIN customeraccount ca ON co.customerid = ca.customerid
   JOIN orderlog ol ON co.orderid = ol.orderid
   JOIN products p ON ol.productcode = p.productcode
   LIMIT 10;
   
   -- Test query para sa new views
   SELECT * FROM vw_sales_by_period LIMIT 10;
   SELECT * FROM vw_product_performance LIMIT 10;
   ```

## Step 7: Update Application Code

1. I-update ang user authentication code para gamitin ang `users` table sa halip na multiple user tables
2. I-update ang order processing code para i-handle ang foreign key constraints
3. I-update ang inventory management functions para gamitin ang proper relationships

## Troubleshooting

### Email Column Issue
- Kung may error tungkol sa 'usermail' column, i-check muna ang exact column name gamit ang:
  ```sql
  SELECT COLUMN_NAME 
  FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = 'dbhenrichfoodcorps' 
  AND TABLE_NAME = 'users';
  ```
- I-edit ang scripts kung kinakailangan para tumugma sa actual column name

### Foreign Key Errors
- Paano i-fix:
  ```sql
  -- Example: Fix orphaned records
  DELETE FROM orderlog
  WHERE NOT EXISTS (
      SELECT 1 FROM customerorder co WHERE co.orderid = orderlog.orderid
  );
  ```

### Duplicate Key Errors
- Gamitin ang data_cleanup_fixed.sql para ma-identify at ma-fix ang duplicate entries

### Constraint Violations
- Pag may constraint violation, i-check kung may data sa dependent table na wala sa parent table
- Pwedeng i-delete ang orphaned records o i-update ang foreign key value to NULL (kung allowed)

## Notes on Email Column Handling

Ang fixed scripts ay gumagamit ng dynamic SQL para automatic na ma-detect at ma-handle ang iba't ibang possible na email column names. Dahil dito, ang scripts ay dapat gumana kahit 'usermail', 'email', o 'useremail' ang column name sa users table.

Kung may mga questions ka o kailangan ng tulong, mag-reference sa documentation o mag-comment sa code repository. 