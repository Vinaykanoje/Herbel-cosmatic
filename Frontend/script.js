/* =========================================
   VANA - PRODUCT & CART SCRIPT
   MYSQL + PHP BACKEND VERSION
   ========================================= */


/* =========================================
   PRODUCT DATA
   ========================================= */

// Products will now come from PHP/MySQL
let P = [];


/* =========================================
   MONEY FORMAT
   ========================================= */

const money = (n) => {
    return "₹" + Number(n).toLocaleString("en-IN");
};


/* =========================================
   LOAD PRODUCTS FROM PHP / MYSQL
   ========================================= */

async function loadProducts() {

    try {

       const response = await fetch("../backend/products.php");
        if (!response.ok) {
            throw new Error(
                `Server error: ${response.status}`
            );
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(
                data.message || "Unable to load products."
            );
        }

        /*
         * Convert MySQL product fields into the
         * format used by the existing VANA frontend.
         */

        P = data.products.map((product) => {

            return {

                id: Number(product.id),

                name: product.name,

                desc: product.description || "",

                price: Number(product.price),

                sku: product.sku || "",

                image: product.image || "",

                stock: Number(product.stock || 0),

                category: product.category || "General",

                /*
                 * These fields are not currently stored
                 * in MySQL, so we provide safe defaults.
                 */

                badge: "Botanical Formula",

                ingredients: []

            };

        });

        console.log(
            "VANA products loaded successfully from backend:",
            P
        );

    } catch (error) {

        console.warn(
            "PHP/MySQL backend unavailable, falling back " +
            "to the static product list in data.js:",
            error
        );

        /*
         * =================================================
         * FALLBACK: STATIC DATA FROM data.js
         * =================================================
         * No backend? No problem. Use the bundled
         * window.VANA_PRODUCTS list so the site still
         * works as a static demo (per the README), and
         * upgrades automatically once the PHP backend
         * is available.
         */

        const staticProducts =
            window.VANA_PRODUCTS || [];

        if (staticProducts.length) {

            P = staticProducts.map((product) => {

                return {

                    id: Number(product.id),

                    name: product.name,

                    desc: product.desc || "",

                    price: Number(product.price),

                    sku: product.sku || "",

                    image: product.image || "",

                    stock: Number(
                        product.stock !== undefined
                            ? product.stock
                            : 25
                    ),

                    category: product.category || "General",

                    badge: product.badge || "Botanical Formula",

                    ingredients: product.ingredients || []

                };

            });

            console.log(
                "VANA products loaded successfully from data.js:",
                P
            );

        } else {

            /*
             * Truly no data available anywhere.
             * Show an error on the shop/product area
             * instead of leaving the page blank.
             */

            const shopGrid =
                document.querySelector("[data-shop]");

            if (shopGrid) {

                shopGrid.innerHTML = `
                    <div class="empty-state">

                        <h2>
                            Unable to load products
                        </h2>

                        <p class="muted">
                            Please make sure the VANA
                            backend and MySQL database
                            are running, or that data.js
                            is included on this page.
                        </p>

                    </div>
                `;
            }

            const featured =
                document.querySelector("[data-featured]");

            if (featured) {

                featured.innerHTML = `
                    <div class="empty-state">

                        <h2>
                            Products unavailable
                        </h2>

                        <p class="muted">
                            Please try again later.
                        </p>

                    </div>
                `;
            }

            return;

        }

    }

    /*
     * Once products are loaded (from either source),
     * render the pages.
     */

    renderFeatured();

    renderShop();

    renderProduct();

    renderCart();

}


/* =========================================
   CART STORAGE
   ========================================= */

const getCart = () => {

    return JSON.parse(
        localStorage.getItem("vanaCart") || "[]"
    );

};


const saveCart = (cart) => {

    localStorage.setItem(
        "vanaCart",
        JSON.stringify(cart)
    );

    updateCartCount();

};


/* =========================================
   CART COUNT
   ========================================= */

function updateCartCount() {

    const cart = getCart();

    const count = cart.reduce(
        (total, item) => {

            return total + Number(item.qty || 0);

        },
        0
    );

    document
        .querySelectorAll("[data-cart-count]")
        .forEach((element) => {

            element.textContent = count;

        });

}


/* =========================================
   ADD TO CART
   ========================================= */

function addToCart(id) {

    const productId = Number(id);

    const cart = getCart();

    const existingProduct = cart.find(
        (item) => item.id === productId
    );


    /*
     * Check whether the product exists
     * in the database-loaded product list.
     */

    const product = P.find(
        (item) => item.id === productId
    );


    if (!product) {

        alert("Product not found.");

        return;

    }


    /*
     * Check stock
     */

    if (Number(product.stock) <= 0) {

        alert("This product is currently out of stock.");

        return;

    }


    if (existingProduct) {

        /*
         * Prevent quantity from exceeding stock.
         */

        if (
            existingProduct.qty >=
            Number(product.stock)
        ) {

            alert(
                `Only ${product.stock} items available in stock.`
            );

            return;

        }

        existingProduct.qty += 1;

    } else {

        cart.push({

            id: productId,

            qty: 1

        });

    }


    saveCart(cart);

    alert("Added to cart.");

}


/* =========================================
   PRODUCT CARD
   ========================================= */

function productCard(product) {

    return `
        <article class="product">

            <!-- Product Link -->

            <a
                class="product-link"
                href="product.html?id=${product.id}"
            >

                <!-- Product Visual -->

                <div class="product-visual">

                    ${
                        product.image
                        ?
                        `
                        <img
                        src="images/${product.image}"
                        alt="${product.name}"
                        loading="lazy"
                         >
                        `
                        :
                        `
                        <div class="mini-bottle">
                            ${product.id}
                        </div>
                        `
                    }

                </div>


                <!-- Product Information -->

                <div class="product-body">

                    <div class="eyebrow">
                        ${product.category}
                    </div>

                    <h3>
                        ${product.name}
                    </h3>

                    <p class="muted">
                        ${product.desc}
                    </p>

                    <span class="badge">
                        ${product.badge}
                    </span>

                    <p class="price">
                        ${money(product.price)}
                    </p>

                </div>

            </a>


            <!-- Add To Cart -->

            <div class="product-action">

                <button
                    class="btn fill"
                    type="button"
                    onclick="addToCart(${product.id})"
                >
                    Add to cart
                </button>

            </div>

        </article>
    `;

}


/* =========================================
   FEATURED PRODUCTS
   ========================================= */

function renderFeatured() {

    const element =
        document.querySelector("[data-featured]");


    if (!element) {

        return;

    }


    if (!P.length) {

        element.innerHTML = `
            <div class="empty-state">
                <h2>
                    No products available
                </h2>
            </div>
        `;

        return;

    }


    element.innerHTML = P
        .slice(0, 4)
        .map(productCard)
        .join("");

}


/* =========================================
   SHOP PRODUCTS
   ========================================= */

function renderShop() {

    const grid =
        document.querySelector("[data-shop]");


    if (!grid) {

        return;

    }


    const search =
        document.querySelector("#search");

    const category =
        document.querySelector("#category");

    const sort =
        document.querySelector("#sort");


    /*
     * Safety check
     */

    if (!search || !category || !sort) {

        console.warn(
            "Shop filters were not found."
        );

        return;

    }


    function filterProducts() {

        let products = P.filter(
            (product) => {

                /*
                 * Search by product name,
                 * description and category.
                 */

                const searchText =
                    `${product.name}
                    ${product.desc}
                    ${product.category}`
                        .toLowerCase();


                const matchesSearch =
                    !search.value ||
                    searchText.includes(
                        search.value.toLowerCase()
                    );


                /*
                 * Category filter
                 */

                const matchesCategory =
                    !category.value ||
                    product.category === category.value;


                return (
                    matchesSearch &&
                    matchesCategory
                );

            }
        );


        /* =====================================
           SORT PRODUCTS
           ===================================== */

        if (sort.value === "low") {

            products.sort(
                (a, b) =>
                    Number(a.price) -
                    Number(b.price)
            );

        }


        if (sort.value === "high") {

            products.sort(
                (a, b) =>
                    Number(b.price) -
                    Number(a.price)
            );

        }


        /* =====================================
           DISPLAY PRODUCTS
           ===================================== */

        if (!products.length) {

            grid.innerHTML = `
                <div class="empty-state">

                    <h2>
                        No products found
                    </h2>

                    <p class="muted">
                        Try another search or category.
                    </p>

                </div>
            `;

            return;

        }


        grid.innerHTML = products
            .map(productCard)
            .join("");

    }


    /*
     * Search event
     */

    search.addEventListener(
        "input",
        filterProducts
    );


    /*
     * Category event
     */

    category.addEventListener(
        "change",
        filterProducts
    );


    /*
     * Sort event
     */

    sort.addEventListener(
        "change",
        filterProducts
    );


    /*
     * Initial display
     */

    filterProducts();

}


/* =========================================
   PRODUCT DETAILS
   ========================================= */

function renderProduct() {

    const element =
        document.querySelector("[data-product]");


    if (!element) {

        return;

    }


    const params =
        new URLSearchParams(
            window.location.search
        );


    const id =
        Number(
            params.get("id") || 1
        );


    const product =
        P.find(
            (item) => item.id === id
        ) || P[0];


    if (!product) {

        element.innerHTML = `
            <div class="empty-state">

                <h2>
                    Product not found
                </h2>

                <a
                    class="btn fill"
                    href="shop.html"
                >
                    Back to shop
                </a>

            </div>
        `;

        return;

    }


    /*
     * Product ingredients
     */

    const ingredients =
        product.ingredients &&
        product.ingredients.length
        ?
        product.ingredients
            .map(
                (ingredient) => `
                    <span class="badge">
                        ${ingredient}
                    </span>
                `
            )
            .join("")
        :
        `
            <span class="badge">
                Natural Botanicals
            </span>
        `;


    element.innerHTML = `

        <div class="two-col product-detail">


            <!-- =================================
                 PRODUCT IMAGE
                 ================================= -->

            <div
                class="product-visual
                       product-detail-visual"
            >

                ${
                    product.image
                    ?
                    `
                    <img
                        src="images/${product.image}"
                        alt="${product.name}"
                    >
                    `
                    :
                    `
                    <div class="product-jar">

                        <span>
                            ${product.sku}
                        </span>

                        <strong>
                            ${product.name}
                        </strong>

                        <small>
                            ${product.badge}
                        </small>

                    </div>
                    `
                }

            </div>


            <!-- =================================
                 PRODUCT INFORMATION
                 ================================= -->

            <div class="product-detail-info">

                <div class="eyebrow">
                    ${product.category}
                </div>

                <h1>
                    ${product.name}
                </h1>

                <p class="price">
                    ${money(product.price)}
                </p>

                <p>
                    ${product.desc}
                </p>


                <h3>
                    Botanical profile
                </h3>


                <div class="ingredient-badges">

                    ${ingredients}

                </div>


                <p class="muted">
                    SKU: ${product.sku}
                </p>


                <p class="muted">
                    ${
                        Number(product.stock) > 0
                        ?
                        `${product.stock} available`
                        :
                        "Out of stock"
                    }
                </p>


                <div class="product-detail-action">

                    <button
                        class="btn fill"
                        type="button"
                        onclick="addToCart(${product.id})"
                        ${
                            Number(product.stock) <= 0
                            ? "disabled"
                            : ""
                        }
                    >
                        ${
                            Number(product.stock) > 0
                            ? "Add to cart"
                            : "Out of stock"
                        }
                    </button>


                    <a
                        class="btn"
                        href="cart.html"
                    >
                        View bag
                    </a>

                </div>

            </div>

        </div>

    `;

}


/* =========================================
   CART
   ========================================= */

function renderCart() {

    const element =
        document.querySelector("[data-cart]");


    if (!element) {

        return;

    }


    function drawCart() {

        const cart = getCart();


        /* =====================================
           EMPTY CART
           ===================================== */

        if (!cart.length) {

            element.innerHTML = `
                <div class="empty-state">

                    <h2>
                        Your apothecary basket is empty.
                    </h2>

                    <p class="muted">
                        Discover botanical formulas
                        for your daily ritual.
                    </p>

                    <a
                        class="btn fill"
                        href="shop.html"
                    >
                        Explore the collection
                    </a>

                </div>
            `;

            return;

        }


        let total = 0;


        /* =====================================
           CART ROWS
           ===================================== */

        const rows = cart
            .map((item) => {

                const product =
                    P.find(
                        (p) =>
                            p.id === Number(item.id)
                    );


                /*
                 * Product was removed from database
                 */

                if (!product) {

                    return "";

                }


                const subtotal =
                    product.price * item.qty;


                total += subtotal;


                return `
                    <div class="cart-row">

                        <div>

                            <h3>
                                ${product.name}
                            </h3>

                            <span class="price">
                                ${money(product.price)}
                            </span>

                        </div>


                        <div class="qty">

                            <button
                                type="button"
                                onclick="changeQty(
                                    ${product.id},
                                    -1
                                )"
                            >
                                −
                            </button>


                            <span>
                                ${item.qty}
                            </span>


                            <button
                                type="button"
                                onclick="changeQty(
                                    ${product.id},
                                    1
                                )"
                            >
                                +
                            </button>

                        </div>


                        <strong>
                            ${money(subtotal)}
                        </strong>

                    </div>
                `;

            })
            .join("");


        /* =====================================
           CART TOTAL
           ===================================== */

        element.innerHTML = `

            ${rows}

            <div class="total">
                Total: ${money(total)}
            </div>


            <a
                class="btn fill checkout-btn"
                href="checkout.html"
            >
                Proceed to checkout
            </a>

        `;

    }


    /* =====================================
       CHANGE QUANTITY
       ===================================== */

    window.changeQty = (
        id,
        amount
    ) => {

        const cart = getCart();


        const productInCart =
            cart.find(
                (item) =>
                    item.id === Number(id)
            );


        if (!productInCart) {

            return;

        }


        const databaseProduct =
            P.find(
                (product) =>
                    product.id === Number(id)
            );


        /*
         * Prevent quantity from exceeding stock
         */

        if (
            amount > 0 &&
            databaseProduct &&
            productInCart.qty >=
                Number(databaseProduct.stock)
        ) {

            alert(
                `Only ${databaseProduct.stock} items available in stock.`
            );

            return;

        }


        productInCart.qty += amount;


        /*
         * Remove products with quantity 0
         */

        const updatedCart =
            cart.filter(
                (item) => item.qty > 0
            );


        saveCart(updatedCart);

        drawCart();

    };


    drawCart();

}

/* =========================================
   CHECKOUT
   SEND ORDER TO PHP / MYSQL
   ========================================= */

function setupCheckout() {

    const form = document.querySelector("#checkoutForm");

    if (!form) {
        return;
    }

    form.addEventListener("submit", async function (event) {

        event.preventDefault();

        const cart = getCart();

        /* =====================================
           CHECK CART
           ===================================== */

        if (!cart.length) {
            alert("Your cart is empty. Please add products first.");
            window.location.href = "shop.html";
            return;
        }

        /* =====================================
           GET FORM DATA
           ===================================== */

        const formData = new FormData(form);

        const orderData = {
            name: formData.get("name"),
            email: formData.get("email"),
            phone: formData.get("phone"),
            address: formData.get("address"),
            payment: formData.get("payment"),
            cart: cart
        };

        /* =====================================
           DISABLE BUTTON
           ===================================== */

        const button = form.querySelector("button[type='submit']");

        const originalText = button.textContent;

        button.disabled = true;
        button.textContent = "Placing Order...";

        try {

            /* =================================
               SEND DATA TO CHECKOUT.PHP
               ================================= */

            const response = await fetch("../backend/checkout.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(orderData)

            });

            const data = await response.json();

            /* =================================
               SUCCESS
               ================================= */

            if (data.success) {

                alert(
                    "Order placed successfully!\n\n" +
                    "Order ID: " + data.order_id + "\n" +
                    "Total: " + money(data.total)
                );

                /* Clear cart */

                localStorage.removeItem("vanaCart");

                updateCartCount();

                /* Go to home page */

                window.location.href = "index.html";

            } else {

                alert(
                    data.message ||
                    "Unable to place your order."
                );

                button.disabled = false;
                button.textContent = originalText;
            }

        } catch (error) {

            console.error(
                "Checkout error:",
                error
            );

            alert(
                "Something went wrong while placing your order. " +
                "Please try again."
            );

            button.disabled = false;
            button.textContent = originalText;
        }

    });

}
/* =========================================
   PAGE INITIALIZATION
   ========================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        updateCartCount();

        loadProducts();

        setupCheckout();

    }
);