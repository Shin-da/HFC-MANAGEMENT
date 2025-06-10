/**
 * Transfer completed orders from orders table to orderhistory table
 */
async function connectToDatabase() {
  try {
    const response = await fetch('...');
    const data = await response.text(); // Change this to text() instead of json()

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    // Create a JavaScript object with a query method
    window.db = {
      query: async (query, params = []) => {
        try {
          const response = await fetch('/Henrich/database/dbquery.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ query, params })
          });
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          const data = await response.json();
          return data;
        } catch (err) {
          throw err;
        }
      }
    };

    console.log('Database connected successfully!');
  } catch (error) {
    console.error('Error connecting to database:', error);
    const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
    toast.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error connecting to database',
    });
  }
}

function transferOrdersToHistory() {
  const query = "SELECT * FROM orders WHERE status = 'Completed'";
  console.log('Query:', query);
  window.db
    .query(query, [])
    .then((results) => {
      console.log('Results:', results);
      if (results.length > 0) {
        results.forEach((order) => {
          transferOrderToHistory(order);
        });
      } else {
        console.log("No orders to transfer. Please add a row to the orders table with a status of 'Completed' to test the script.");
        const toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
        toast.fire({
          icon: 'info',
          title: 'No orders',
          text: 'No orders to transfer',
        });
      }
    })
    .catch((err) => {
      console.error(err);
      const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });
      toast.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error fetching orders',
      });
    });
}

async function transferOrderToHistory(order) {
  try {
    console.log(order);
    const insertQuery = "INSERT INTO orderhistory (oid, customerid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, status, salesperson, datecompleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    const orderData = [
      order.oid,
      order.customerid,
      order.customername,
      order.customeraddress,
      order.customerphonenumber,
      order.orderdescription,
      order.ordertotal,
      order.orderdate,
      order.status,
      'online', // salesperson
      new Date().toISOString() // date completed
    ];

    const result = await window.db.query(insertQuery, orderData);

    console.log(`Order transferred to orderhistory table. Insert ID: ${result.insertId}`);

    // Delete the selected row from the orders table
    const deleteQuery = "DELETE FROM orders WHERE oid = ?";
    const deleteData = [order.oid];
    await window.db.query(deleteQuery, deleteData);

    console.log(`Order deleted from orders table. OID: ${order.oid}`);

    const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
    toast.fire({
      icon: 'success',
      title: 'Success',
      text: 'Order transferred',
    });

  } catch (err) {
    console.error(err);
    const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
    toast.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error transferring order',
    });
  }
}

// Run the function on start
connectToDatabase().then(() => {
  transferOrdersToHistory();
});


