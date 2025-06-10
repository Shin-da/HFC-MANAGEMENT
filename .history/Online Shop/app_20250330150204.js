// DOM Elements
const iconCart = document.querySelector('.icon-cart');
const closeCart = document.querySelector('.close');
const body = document.querySelector('body');
const listProductHTML = document.querySelector('.listProduct');
const listCartHTML = document.querySelector('.listCart');
const iconCartSpan = document.querySelector('.icon-cart span');

// State
let listProducts = [];
let carts = JSON.parse(localStorage.getItem('carts')) || [];
let isCartOpen = JSON.parse(localStorage.getItem('isCartOpen')) || false;

if (carts.length > 0) {
    iconCartSpan.textContent = carts.length;
}

if (isCartOpen) {
    listCartHTML.parentElement.style.display = "block";
}

// Fetch account ID and user details from session
const fetchAccountDetails = async () => {
    try {
        const response = await fetch('./session/session.php', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('response:', response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const parsedData = await response.json();
        console.log('parsedData:', parsedData);
        return parsedData;
    } catch (error) {
        console.error('Error fetching account details:', error);
        return null;
    }    
}

// Fetch products from server
const fetchProducts = async () => {
    try {
        const response = await fetch('fetchproduct.php');
        console.log('Response:', response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        console.log('Fetched products:', data);
        return data;
    } catch (error) {
        console.error('Error fetching products:', error);
        return [];
    }
};

// Initialize application
const initApp = async () => {
    console.log('Initializing application');
    const products = await fetchProducts();
    if (products && products.length > 0) {
        listProducts = products;
        displayProducts(listProducts);
        addCartHTML();
    } else {
        console.log('No products found');
        const productList = document.getElementById('product-list');
        if (productList) {
            productList.innerHTML = '<p style="text-align: center; color: red;">No products available</p>';
        }
    }
};

// Display products on the page
const displayProducts = products => {
    console.log('Displaying products on the page');
    const productList = document.getElementById('product-list');
    if (!productList) {
        console.error('Product list container not found');
        return;
    }
    
    productList.innerHTML = ''; // Clear existing products
    
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.className = 'product';
        productDiv.dataset.id = product.productcode;
        // Format price to 2 decimal places
        const formattedPrice = parseFloat(product.productprice).toFixed(2);
        productDiv.innerHTML = `
            <img src="./resources/images/${product.productimage || 'placeholder-image.png'}" alt="${product.productname}" />
            <h2>${product.productname} (${product.productweight ? product.productweight : ''}kg)</h2>
            <p>Price: ₱${formattedPrice}</p>
            <button class="addCart">Add to Cart</button>
        `;
        productList.appendChild(productDiv);

        const addCartButton = productDiv.querySelector('.addCart');
        addCartButton.addEventListener('click', () => {
            console.log('Adding to cart');
            addToCart(product.productcode, product.productweight);
            iconCartSpan.textContent = carts.length;
        });
    });
};

// Add product to cart
const addToCart = (clickedProductId, productWeight) => {
    console.log('Adding product to cart:', clickedProductId, productWeight);
    const position = carts.findIndex(cart => cart.productId === clickedProductId);
    if (position >= 0) {
        carts[position].quantity += 1;
    } else {
        carts.push({ productId: clickedProductId, quantity: 1, productWeight: productWeight });
    }
    localStorage.setItem('carts', JSON.stringify(carts));
    addCartHTML();
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    Toast.fire({
        title: 'Success',
        text: 'Item added to cart successfully.',
        icon: 'success',
    });
};

// Remove product from cart
const removeFromCart = productId => {
    console.log('Removing product from cart:', productId);
    carts = carts.filter(cart => cart.productId !== productId);
    localStorage.setItem('carts', JSON.stringify(carts));
    addCartHTML();
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    Toast.fire({
        icon: 'success',
        title: 'Item removed from cart successfully.',
    });
};

// Update cart HTML
const addCartHTML = () => {
    console.log('Updating cart HTML');
    listCartHTML.innerHTML = '';
    const cartItemsContainer = document.createElement('div');
    cartItemsContainer.classList.add('items');
    listCartHTML.appendChild(cartItemsContainer);

    let totalQuantity = 0;
    let totalPrice = 0;

    carts.forEach(cart => {
        const position = listProducts.findIndex(product => product.productcode === cart.productId);
        const info = listProducts[position];
        if (!info) {
            console.error('Product not found:', cart.productId);
            return;
        }
        
        totalQuantity += cart.quantity;
        const itemPrice = parseFloat(info.productprice) * cart.quantity;
        totalPrice += itemPrice;

        const newCart = document.createElement('div');
        newCart.classList.add('item');
        newCart.innerHTML = ` 
        <i class=' remove bx bx-trash' aria-hidden='true'></i>
            <div class="image">
            <img src="./resources/images/${info.productimage || 'placeholder-image.png'}" alt="${info.productname}">
            </div>
            <div class="name">${info.productname} (${info.productweight ? info.productweight : ''}kg)</div>
            <div class="totalPrice">₱${itemPrice.toFixed(2)}</div>
            <div class="quantity">
                <span class="minus">-</span>
                <span>${cart.quantity}</span>
                <span class="plus">+</span>
            </div>
        `;
        cartItemsContainer.appendChild(newCart);

        newCart.querySelector('.remove').addEventListener('click', (event) => {
            event.stopPropagation();
            removeFromCart(cart.productId);
        });

        newCart.querySelector('.plus').addEventListener('click', (event) => {
            event.stopPropagation();
            updateCartQuantity(cart.productId, 1);
        });

        newCart.querySelector('.minus').addEventListener('click', (event) => {
            event.stopPropagation();
            updateCartQuantity(cart.productId, -1);
        });
    });

    const previousTotal = document.querySelector('.total');
    if (previousTotal) {
        previousTotal.remove();
    }

    const totalDisplay = document.createElement('div');
    totalDisplay.classList.add('total');

    const quantityTotal = document.createElement('div');
    quantityTotal.classList.add('quantityTotal');
    quantityTotal.innerHTML = `<h3>Total Quantity: ${totalQuantity}</h3>`;

    const priceTotal = document.createElement('div');
    priceTotal.classList.add('priceTotal');
    priceTotal.innerHTML = `<h3>Total Price: ₱${totalPrice.toFixed(2)}</h3>`;

    totalDisplay.appendChild(quantityTotal);
    totalDisplay.appendChild(priceTotal);

    const cartFooter = document.querySelector('.cart-footer');
    cartFooter.appendChild(totalDisplay);
};

// Update cart quantity
const updateCartQuantity = (productId, change) => {
    console.log('Updating cart quantity:', productId, change);
    const position = carts.findIndex(cart => cart.productId === productId);
    if (position >= 0) {
        const newQuantity = carts[position].quantity + change;
        if (newQuantity > 0) {
            carts[position].quantity = newQuantity;
        } else {
            removeFromCart(productId);
        }
    }
    localStorage.setItem('carts', JSON.stringify(carts));
    addCartHTML();
};

// Toggle cart display
iconCart.addEventListener('click', function (event) {
    console.log('Toggling cart display');
    event.stopPropagation();
    listCartHTML.parentElement.style.display = listCartHTML.parentElement.style.display === "none" || listCartHTML.parentElement.style.display === "" ? "block" : "none";
});

closeCart.addEventListener('click', function (event) {
    console.log('Closing cart');
    listCartHTML.parentElement.style.display = "none";
});

document.addEventListener('click', function (event) {
    console.log('Document click');
    if (!listCartHTML.parentElement.contains(event.target) && event.target !== iconCart) {
        console.log('Closing cart');
        listCartHTML.parentElement.style.display = "none";
    }
});
document.getElementById("confirm-order")?.addEventListener("click", function () {
    // Close the checkout modal and show the confirmation modal
    toggleModalDisplay("checkout-modal", "confirmation-modal");
});

document.getElementById("confirm-purchase")?.addEventListener("click", async function () {
    // Hide the confirmation modal
    toggleModalDisplay("confirmation-modal");

    // Fetch account details and place the order
    const accountDetailsResponse = await fetchAccountDetails();
    const { accountid, customername, customeraddress, customerphonenumber } = accountDetailsResponse;
    console.log('accountDetails after assignment:', { accountid, customername, customeraddress, customerphonenumber });

    if (!accountid) {
        console.error('Failed to fetch account details');
        Swal.fire({
            title: 'Error',
            text: 'You must be logged in to place an order.',
            icon: 'error',
        });
        return;
    }

    // Create the order data object
    const orderDescription = carts.map(cart => {
        const product = listProducts.find(p => p.productcode === cart.productId);
        if (product) {
            return { productname: product.productname, quantity: cart.quantity };
        }
    }).filter(Boolean);

    console.log('Order Description:', JSON.stringify(orderDescription, null, 2));

    const orderTotal = carts.reduce((total, cart) => {
        const product = listProducts.find(p => p.productcode === cart.productId);
        if (product) {
            return total + (parseFloat(product.productprice) * cart.quantity);
        }
        return total;
    }, 0);

    const dataToPost = {
        customerName: customername,
        customerAddress: customeraddress,
        customerPhone: customerphonenumber,
        orderDescription,
        orderTotal: orderTotal.toFixed(2),
    };
    console.log('Order data:', dataToPost);
    console.log('Running placeOrder.php');
    fetch('placeOrder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dataToPost),
    })
        .then(response => response.text())
        .then((data) => JSON.parse(data))
        .then((data) => {
            if (data.success) {
                console.log('Order placed successfully');
                const orderDetails = orderDescription.map(item => {
                    const product = listProducts.find(p => p.productname === item.productname);
                    if (product) {
                        return `  ${item.productname} (${product.productweight}g) - x${item.quantity}`;
                    } else {
                        console.error(`Product not found for ${item.productname}`);
                        return `  ${item.productname} - Product not found (x${item.quantity})`;
                    }
                }).join('\n');
                Swal.fire({
                    title: 'Order Placed',
                    text: `Order placed successfully!\n\nOrder Details:\n${orderDetails}\nTotal: ₱${orderTotal.toFixed(2)}`,
                    icon: 'success',
                });
                carts.length = 0;
                localStorage.setItem('carts', JSON.stringify(carts));
                addCartHTML();
                setTimeout(function() {
                    location.href = 'ordersuccess.php';
                }, 3000);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: `Error placing order: ${data.message}`,
                    icon: 'error',
                });
            }

        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error placing order. Please try again.',
                icon: 'error',
            });
        });
});

document.getElementById("cancel-confirmation")?.addEventListener("click", function () {
    // Close the confirmation modal and show the checkout modal
    toggleModalDisplay("confirmation-modal", "checkout-modal");
});

function toggleModalDisplay(hideModalId, showModalId) {
    const hideModal = document.getElementById(hideModalId);
    if (hideModal) {
        hideModal.style.display = "none";
    }
    if (showModalId) {
        const showModal = document.getElementById(showModalId);
        if (showModal) {
            showModal.style.display = "block";
        }
    }
}

// Search products
function searchProducts() {
    console.log('Searching products');
    const searchInput = document.getElementById('search-input')?.value.toLowerCase();
    const products = document.querySelectorAll('.product-item');
    products.forEach(product => {
        const productName = product.querySelector('.product-details h2')?.textContent.toLowerCase();
        if (productName.includes(searchInput)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Initialize the application when the page loads
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing app');
    initApp();
});



