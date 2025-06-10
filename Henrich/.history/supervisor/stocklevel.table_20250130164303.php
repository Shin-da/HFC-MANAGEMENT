$sql = "SELECT 
    i.productcode,
    i.productname,
    i.productweight,    // Add this line
    i.productcategory,
    i.availablequantity,
    i.onhandquantity,
    i.unit_price,
    i.dateupdated,
    CASE 
        WHEN i.availablequantity = 0 THEN 'Out of Stock'
        WHEN i.availablequantity <= 10 THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM inventory i";
