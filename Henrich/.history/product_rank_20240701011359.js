//this is for analytics, determining what is the most bought product

 function getMostBoughtProduct() {
    const response =  fetch('http://localhost/HenrichProto/database/dbconnect.php');
    const data = await response.json();

    const sql = `SELECT ProductCode, SUM(Quantity) as totalQuantity FROM orders GROUP BY ProductCode ORDER BY totalQuantity DESC LIMIT 1`;

    const result = data.query(sql);

    return result[0];
}
