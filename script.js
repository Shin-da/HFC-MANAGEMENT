var confirmButton = document.querySelector('button[type="submit"]');
var total = 0;

confirmButton.addEventListener("click", function (event) {
    console.log("Button clicked!");
    event.preventDefault();  // Prevent form submission until confirmed

    // Show the order summary in the modal
    var cartItems = document.querySelectorAll(".item");
    var orderDetails = document.querySelector(".order-details");
    orderDetails.innerHTML = '';  // Clear any previous summary

    // Initialize the total variable
    total = 0;

    // Populate the order summary with cart items
    cartItems.forEach((item) => {
        var nameElement = item.querySelector(".name");
        var name = nameElement ? nameElement.textContent : "Unknown";

        var quantityElement = item.querySelector(".quantity span:nth-child(2)");
        var quantity = quantityElement ? parseInt(quantityElement.textContent) : 0;

        var priceElement = item.querySelector(".totalPrice");
        var price = priceElement ? parseFloat(priceElement.textContent.replace(/[^0-9.-]+/g, "")) : 0;

        var itemSummary = document.createElement("div");
        itemSummary.classList.add("order-item");
        itemSummary.innerHTML = `
          ${name} x ${quantity} - <span class="total-price">₱${price}</span>
        `;
        orderDetails.appendChild(itemSummary);
        total += price;
    });

    // Display total price
    var totalSummary = document.createElement("div");
    totalSummary.innerHTML = `Total: $${total}`;
    orderDetails.appendChild(totalSummary);

    // Show the modal with the order summary
    document.getElementById("order-summary-modal").style.display = "block";
});

// Add event listener to the 'Back to Cart' button
document.getElementById("back-to-cart").addEventListener("click", function () {
    // Hide the modal and show the cart again
    document.getElementById("order-summary-modal").style.display = "none";
});

// Remove CLOSE button redirection
document.getElementById("close-modal").addEventListener("click", function () {
    // Close the modal without redirection
    document.getElementById("order-summary-modal").style.display = "none";
});


