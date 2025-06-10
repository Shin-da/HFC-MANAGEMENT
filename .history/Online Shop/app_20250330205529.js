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
    
    if (!products || products.length === 0) {
        productList.innerHTML = '<p style="text-align: center; color: red;">No products available</p>';
        return;
    }
    
    products.forEach(product => {
        const productDiv = document.createElement('div');
        productDiv.className = 'product';
        productDiv.dataset.id = product.productcode;
        // Format price to 2 decimal places
        const formattedPrice = parseFloat(product.productprice).toFixed(2);
        productDiv.innerHTML = `
            <img src="./resources/images/${product.productimage || 'placeholder-image.png'}" alt="${product.productname}" />
            <h2>${product.productname}</h2>
            <p>Weight: ${product.productweight} kg</p>
            <p>Price: ₱${formattedPrice}</p>
            <p>Pieces per Box: ${product.piecesperbox}</p>
            <button class="addCart">Add to Cart</button>
        `;
        productList.appendChild(productDiv);

        const addCartButton = productDiv.querySelector('.addCart');
        addCartButton.addEventListener('click', () => {
            console.log('Adding to cart:', product);
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

// Add debug logging function
const debugLog = (message, data = null) => {
    const timestamp = new Date().toISOString();
    console.log(`[${timestamp}] ${message}`, data || '');
};

// Update validateOrder function with debug logging
const validateOrder = async () => {
    debugLog('Starting order validation');
    
    // 1. Check if cart is empty
    debugLog('Checking cart contents', carts);
    if (!carts.length) {
        debugLog('Cart is empty');
        Swal.fire({
            title: 'Error',
            text: 'Your cart is empty',
            icon: 'error'
        });
        return false;
    }

    // 2. Check if user is logged in
    debugLog('Checking user login status');
    const accountDetails = await fetchAccountDetails();
    debugLog('Account details received', accountDetails);
    
    if (!accountDetails || !accountDetails.accountid) {
        debugLog('User not logged in');
        Swal.fire({
            title: 'Error',
            text: 'Please log in to place an order',
            icon: 'error'
        });
        return false;
    }

    // 3. Validate customer details
    debugLog('Validating customer details');
    if (!accountDetails.customername || !accountDetails.customeraddress || !accountDetails.customerphonenumber) {
        debugLog('Incomplete customer details');
        Swal.fire({
            title: 'Error',
            text: 'Please complete your profile information',
            icon: 'error'
        });
        return false;
    }

    // 4. Validate each cart item
    debugLog('Validating cart items');
    for (const cart of carts) {
        const product = listProducts.find(p => p.productcode === cart.productId);
        debugLog('Checking product', { cart, product });
        
        if (!product) {
            debugLog('Product not found', cart.productId);
            Swal.fire({
                title: 'Error',
                text: `Product ${cart.productId} not found`,
                icon: 'error'
            });
            return false;
        }

        // Check if quantity is valid
        if (cart.quantity <= 0) {
            debugLog('Invalid quantity', { product: product.productname, quantity: cart.quantity });
            Swal.fire({
                title: 'Error',
                text: `Invalid quantity for ${product.productname}`,
                icon: 'error'
            });
            return false;
        }

        // Check if quantity exceeds pieces per box
        if (cart.quantity > product.piecesperbox) {
            debugLog('Quantity exceeds stock', { 
                product: product.productname, 
                quantity: cart.quantity, 
                maxStock: product.piecesperbox 
            });
            Swal.fire({
                title: 'Error',
                text: `Quantity exceeds available stock for ${product.productname}`,
                icon: 'error'
            });
            return false;
        }
    }

    // 5. Validate total price
    debugLog('Calculating order total');
    const orderTotal = carts.reduce((total, cart) => {
        const product = listProducts.find(p => p.productcode === cart.productId);
        if (product) {
            return total + (parseFloat(product.productprice) * cart.quantity);
        }
        return total;
    }, 0);

    debugLog('Order total calculated', orderTotal);

    if (orderTotal <= 0) {
        debugLog('Invalid order total');
        Swal.fire({
            title: 'Error',
            text: 'Invalid order total',
            icon: 'error'
        });
        return false;
    }

    debugLog('Order validation successful');
    return true;
};

// Update the confirm purchase event listener
document.getElementById("confirm-purchase")?.addEventListener("click", async function () {
    try {
        // Validate order before proceeding
        const isValid = await validateOrder();
        if (!isValid) {
            return;
        }

        // Fetch account details
        const accountDetailsResponse = await fetchAccountDetails();
        const { accountid, customername, customeraddress, customerphonenumber } = accountDetailsResponse;

        // Create order description with additional validation
        const orderDescription = carts.map(cart => {
            const product = listProducts.find(p => p.productcode === cart.productId);
            if (!product) {
                throw new Error(`Product ${cart.productId} not found`);
            }
            return {
                productcode: product.productcode,
                productname: product.productname,
                unit_price: parseFloat(product.productprice),
                quantity: parseInt(cart.quantity)
            };
        }).filter(Boolean);

        // Calculate total with validation
        const orderTotal = carts.reduce((total, cart) => {
            const product = listProducts.find(p => p.productcode === cart.productId);
            if (!product) {
                throw new Error(`Product ${cart.productId} not found`);
            }
            return total + (parseFloat(product.productprice) * parseInt(cart.quantity));
        }, 0);

        // Prepare data for order placement
        const dataToPost = {
            customerName: customername,
            customerAddress: customeraddress,
            customerPhone: customerphonenumber,
            orderDescription: orderDescription,
            orderTotal: parseFloat(orderTotal.toFixed(2))
        };

        console.log('Order data:', JSON.stringify(dataToPost, null, 2));

        // Send order to server
        const response = await fetch('placeOrder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dataToPost)
        });

        // Get response text first
        const responseText = await response.text();
        console.log('Raw server response:', responseText);

        // Try to parse the response
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse server response:', e);
            throw new Error('Invalid server response');
        }

        console.log('Parsed server response:', result);

        if (!response.ok) {
            throw new Error(result.message || 'Failed to place order');
        }

        if (result.success) {
            // Clear cart and show success message
            localStorage.removeItem('carts');
            carts = [];
            addCartHTML();
            iconCartSpan.textContent = '0';

            // Show success message
            Swal.fire({
                title: 'Order Placed',
                text: `Order placed successfully!\n\nOrder Details:\n${orderDescription.map(item => 
                    `${item.productname} x ${item.quantity}`
                ).join('\n')}\nTotal: ₱${orderTotal.toFixed(2)}`,
                icon: 'success',
            }).then(() => {
                // Redirect to order success page
                window.location.href = 'ordersuccess.php';
            });
        } else {
            throw new Error(result.message || 'Failed to place order');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Error placing order. Please try again.',
            icon: 'error'
        });
    }
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



