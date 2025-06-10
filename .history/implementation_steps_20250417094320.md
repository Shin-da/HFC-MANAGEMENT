# Implementation Steps for Database Restructuring through phpMyAdmin

Dahil hindi natin ma-execute ang scripts direkta sa command line, sundin ang mga steps na ito gamit ang phpMyAdmin:

## Step 1: Backup ng Database

1. Buksan ang phpMyAdmin sa browser (http://localhost/phpmyadmin/)
2. I-click ang database "dbhenrichfoodcorps" sa left sidebar
3. I-click ang "Export" tab sa top menu
4. Piliin ang "Custom" option para sa export method
5. Siguraduhing naka-check ang "Add DROP TABLE" at "Add CREATE TABLE"
6. I-click ang "Go" button para i-download ang backup SQL file

## Step 2: Execute ang Data Integrity Check

1. Sa phpMyAdmin, i-click ang database "dbhenrichfoodcorps"
2. I-click ang "SQL" tab
3. Buksan ang file `data_integrity_check.sql` gamit ang text editor
4. I-copy at i-paste ang contents nito sa SQL query textarea
5. I-click ang "Go" button para ma-execute
6. I-check ang results para ma-identify ang mga data issues

## Step 3: Ayusin ang Data Issues

1. Sa phpMyAdmin, i-click ang database "dbhenrichfoodcorps"
2. I-click ang "SQL" tab
3. Buksan ang file `data_cleanup.sql` gamit ang text editor
4. I-copy at i-paste ang contents nito sa SQL query textarea
5. I-click ang "Go" button para ma-execute
6. I-check kung may error messages at i-fix kung meron

## Step 4: Implement ang Schema Changes (By Section)

Para maiwasan ang mga error, i-implement natin ang schema changes section by section:

### 4.1 Backup Statements
1. Buksan ang `db_schema_fixes.sql` sa text editor
2. I-copy ang section na "BACKUP STATEMENTS" (lines 5-35) sa SQL textarea ng phpMyAdmin
3. I-execute ito

### 4.2 Fix User Tables
1. I-copy ang section na "FIX USER TABLES" (lines 37-83) sa SQL textarea ng phpMyAdmin
2. I-execute ito
3. I-check kung may error messages at ayusin kung meron

### 4.3 Fix Customer Tables
1. I-copy ang section na "FIX CUSTOMER TABLES" (lines 85-96) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 4.4 Fix Order Tables
1. I-copy ang section na "FIX ORDER TABLES" (lines 98-122) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 4.5 Fix Inventory Tables
1. I-copy ang section na "FIX INVENTORY TABLES" (lines 124-145) sa SQL textarea ng phpMyAdmin
2. I-execute ito

### 4.6 Add Indexes and Views
1. I-copy ang section na "ADD INDEXES FOR PERFORMANCE" at "CREATE NEW VIEWS FOR REPORTING" (lines 158-205) sa SQL textarea ng phpMyAdmin
2. I-execute ito

## Step 5: Verify ang Changes

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

## Step 6: Update Application Code

Sundin ang mga recommendations sa `database_restructuring_guide.md` para i-update ang application code para tumugma sa bagong database structure.

## Troubleshooting

Kung may mga error na nangyari habang ini-execute ang scripts:

1. **Foreign Key Errors**: 
   - Siguraduhing naayos mo ang data integrity issues gamit ang data_cleanup.sql
   - Baka kailangan mong manually i-update ang mga specific records

2. **Duplicate Key Errors**:
   - I-check ang affected tables at alisin ang duplicate entries

3. **Table Doesn't Exist**:
   - Siguraduhing ginagawa mo ang mga steps sa tamang sequence

Kung may mga questions ka o kailangan ng tulong, mag-reference sa documentation o mag-comment sa code repository. 