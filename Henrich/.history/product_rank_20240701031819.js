//this is for analytics, determining what is the most bought product

 function getMostBoughtProduct() {
    const response =  fetch('localhost/HenrichProto/database/dbconnect.php');
    const data =  response.json();

    const sql = `SELECT ProductCode, SUM(Quantity) as totalQuantity FROM orders GROUP BY ProductCode ORDER BY totalQuantity DESC LIMIT 1`;

    const result = data.query(sql);
    

    if (result.length === 0) {
        return null;
    } 

    console.log(result[0]);

    return result[0];
}
