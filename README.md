# Herbel-cosmatic
# VANA — Botanical Apothecary

VANA is a full-stack botanical beauty e-commerce website built around natural and herbal skincare, haircare, body-care, and beauty products.

The project combines a responsive HTML/CSS/JavaScript frontend with a PHP/MySQL backend for user authentication, product management, cart/order functionality, and database storage.

---

## 🌿 Project Overview

VANA — Botanical Apothecary provides an online shopping experience for botanical and herbal beauty products.

Users can:

* Browse botanical products
* Search and filter products
* Browse products by category
* View individual product details
* Add products to a shopping cart
* Persist cart data using browser LocalStorage
* Register and log in to an account
* Proceed through checkout
* Select a payment method
* Place orders
* Access their account
* Log out securely
* Explore botanical ingredients and their information

The project is designed to work both as a **PHP/MySQL e-commerce application** and as a **frontend demonstration using static product data**.

---

# ✨ Features

## Core Features

* User registration
* User login/logout
* Session-based authentication
* Product catalogue
* Product categories
* Product detail pages
* Product search
* Product filtering
* Product sorting
* Shopping cart
* LocalStorage cart persistence
* Stock checking
* Checkout form
* Order creation
* Order management
* User account page
* Responsive design
* MySQL database integration
* PHP backend APIs
* Botanical ingredient information

---

## 🛍️ Product Features

Products contain information such as:

* Product name
* Category
* Description
* Price
* SKU
* Product image
* Stock quantity
* Product badge
* Ingredients

The frontend currently contains the following products in `Frontend/data.js`:

| Product              | Category  | Price |
| -------------------- | --------- | ----: |
| Aloe Ritual Cleanser | Face Care |  ₹699 |
| Turmeric Glow Serum  | Face Care |  ₹899 |
| Neem Clay Mask       | Face Care |  ₹599 |
| Brahmi Scalp Oil     | Hair Care |  ₹749 |
| Sandalwood Body Balm | Body Care |  ₹799 |
| Rose & Vetiver Mist  | Beauty    |  ₹649 |

> Note: The SQL database currently contains sample records for four products, while `Frontend/data.js` contains six frontend products. If all six products are required in the database, they should also be inserted into `vana.sql`.

---

# 🛒 Shopping Cart

The shopping cart is implemented using JavaScript and browser LocalStorage.

Cart information is stored under:

```text
vanaCart
```

The cart supports:

* Adding products
* Removing products
* Increasing quantities
* Decreasing quantities
* Quantity validation
* Stock validation
* Automatic cart total calculation
* Persistent cart data between page visits

The main cart logic is implemented in:

```text
Frontend/script.js
```

---

# 👤 User Authentication

VANA includes PHP-based authentication.

### Registration

Users can create an account using:

```text
Frontend/register.html
```

The request is processed by:

```text
backend/register.php
```

### Login

Users can log in through:

```text
Frontend/login.html
```

The login request is handled by:

```text
backend/login.php
```

### Logout

Logout is handled by:

```text
backend/logout.php
```

PHP sessions are used to maintain the logged-in user's identity.

The account page checks for an active session before displaying account information.

---

# 👨‍💻 User Account

Authenticated users can access:

```text
Frontend/account.php
```

The account page displays:

* User name
* Email address
* Account status
* Continue Shopping option
* Logout option

Unauthenticated users are redirected to the login page.

---

# 📦 Checkout & Orders

The checkout page is:

```text
Frontend/checkout.html
```

Customers can enter:

* Full name
* Email
* Phone number
* Delivery address
* Payment method

Available payment method selections currently include:

* Cash on Delivery
* UPI
* Card

Order-related backend files include:

```text
backend/checkout.php
backend/place_order.php
backend/order.php
```

`place_order.php` is the preferred order-placement endpoint because it is designed to associate the order with the authenticated user's `user_id`.

---

# 🌱 Ingredients

VANA includes a dedicated ingredients section.

The ingredients currently represented in `Frontend/data.js` include:

* Aloe Vera
* Neem
* Turmeric
* Brahmi
* Amla
* Tulsi
* Rose
* Vetiver
* Sandalwood
* Green Tea
* Licorice
* Saffron

Ingredient-related images are stored in:

```text
Frontend/images/ingredients/
```

---

# 📄 Website Pages

| Page        | File               | Description                |
| ----------- | ------------------ | -------------------------- |
| Home        | `index.html`       | Main landing page          |
| Shop        | `shop.html`        | Product catalogue          |
| Product     | `product.html`     | Individual product details |
| Cart        | `cart.html`        | Shopping cart              |
| Checkout    | `checkout.html`    | Order checkout             |
| Account     | `account.php`      | Logged-in user account     |
| Login       | `login.html`       | User login                 |
| Register    | `register.html`    | New user registration      |
| About       | `about.html`       | VANA story/about section   |
| Categories  | `categories.html`  | Product categories         |
| Ingredients | `ingredients.html` | Botanical ingredients      |
| Contact     | `contact.html`     | Contact page               |

---

# 🏗️ Project Structure

```text
VANA/
│
├── Frontend/
│   ├── index.html
│   ├── shop.html
│   ├── product.html
│   ├── cart.html
│   ├── checkout.html
│   ├── account.php
│   ├── login.html
│   ├── register.html
│   ├── about.html
│   ├── categories.html
│   ├── ingredients.html
│   ├── contact.html
│   ├── data.js
│   ├── script.js
│   ├── style.css
│   ├── README.md
│   │
│   └── images/
│       ├── aloe-cleanser.png
│       ├── brahmi-oil.png
│       ├── sandalwood-balm.png
│       ├── turmeric-serum.png
│       │
│       └── ingredients/
│           ├── Aloe Vera plant.png
│           ├── Amla fruit.png
│           ├── Brahmi plant.png
│           ├── Green tea leaves.png
│           ├── Licorice root.png
│           ├── Neem leaves.png
│           ├── Rose flower.png
│           ├── Saffron.png
│           ├── Sandalwoodtree.png
│           ├── Tulsi leaves.png
│           ├── Turmeric rootpowder.png
│           ├── Vetiver roots.png
│           └── tree-bg.png
│
├── backend/
│   ├── config.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── checkout.php
│   ├── place_order.php
│   ├── order.php
│   ├── products.php
│   └── test.php
│
└── DataBase/
    └── vana.sql
```

---

# 🛠️ Technology Stack

## Frontend

| Technology   | Purpose                       |
| ------------ | ----------------------------- |
| HTML5        | Page structure                |
| CSS3         | Styling and responsive layout |
| JavaScript   | Frontend functionality        |
| LocalStorage | Shopping cart persistence     |

## Backend

| Technology   | Purpose                           |
| ------------ | --------------------------------- |
| PHP          | Server-side processing            |
| PDO          | MySQL database connection         |
| PHP Sessions | Authentication/session management |
| MySQL        | Persistent application data       |

## Development Environment

Recommended:

* XAMPP or WAMP on Windows
* Apache
* MySQL
* phpMyAdmin
* Modern web browser
* VS Code or another code editor

---

# 🗄️ Database

The database is defined in:

```text
DataBase/vana.sql
```

The default database name is:

```text
vana_db
```

## Database Tables

### `users`

Stores registered users.

Important fields:

* `id`
* `name`
* `email`
* `password`
* `role`
* `created_at`

### `categories`

Stores product categories.

Important fields:

* `id`
* `name`
* `description`
* `created_at`

### `products`

Stores product information.

Important fields:

* `id`
* `category_id`
* `name`
* `description`
* `price`
* `sku`
* `image`
* `stock`
* `created_at`

### `cart`

Stores user shopping carts.

### `cart_items`

Stores individual products and quantities inside carts.

### `orders`

Stores customer orders and delivery information.

### `order_items`

Stores the products associated with each order.

### `reviews`

Provides database support for product reviews and ratings.

---

# ⚙️ Database Configuration

Database configuration is located in:

```text
backend/config.php
```

The current configuration uses:

```text
Host: localhost
Database: vana_db
Username: root
Password: empty
```

For a local XAMPP installation, this configuration commonly works without modification.

For other environments, update the database credentials in:

```text
backend/config.php
```

---

# 🚀 Installation

## 1. Install XAMPP

Install XAMPP with:

* Apache
* MySQL
* PHP

Start both:

```text
Apache
MySQL
```

---

## 2. Copy the Project

Copy the VANA project folder into:

```text
C:\xampp\htdocs\
```

The final structure should look similar to:

```text
C:\xampp\htdocs\VANA\
```

---

## 3. Create the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Import:

```text
DataBase/vana.sql
```

The SQL file automatically creates:

```text
vana_db
```

along with its tables and sample data.

---

## 4. Verify Database Connection

Open:

```text
http://localhost/VANA/backend/test.php
```

If the configuration is correct, the database connection should succeed.

---

# 🌐 Running the Application

Open the frontend using Apache rather than directly opening the HTML files.

For example:

```text
http://localhost/VANA/Frontend/index.html
```

Other pages:

```text
http://localhost/VANA/Frontend/shop.html
http://localhost/VANA/Frontend/cart.html
http://localhost/VANA/Frontend/login.html
http://localhost/VANA/Frontend/register.html
http://localhost/VANA/Frontend/account.php
```

---

# 🔌 Backend Endpoints

## Authentication

### Login

```text
POST /backend/login.php
```

Authenticates an existing user and creates a PHP session.

### Registration

```text
POST /backend/register.php
```

Creates a new user account.

### Logout

```text
GET /backend/logout.php
```

Destroys the current PHP session.

---

## Products

```text
GET /backend/products.php
```

Used to retrieve product information from the database.

The frontend also contains a static fallback using:

```text
Frontend/data.js
```

This allows the product catalogue to continue functioning as a frontend demonstration when the PHP/MySQL backend is unavailable.

---

## Orders

### Place Order

```text
POST /backend/place_order.php
```

Creates an order for an authenticated user.

### Checkout

```text
POST /backend/checkout.php
```

Handles checkout/order processing.

### Order Information

```text
GET /backend/order.php
```

Used for order-related information for authenticated users.

---

# 🔄 Product Data Fallback

The frontend has an important fallback mechanism.

Normally, product information can be loaded from the PHP/MySQL backend.

If the backend is unavailable, the JavaScript application falls back to:

```text
Frontend/data.js
```

The static product list is stored in:

```javascript
window.VANA_PRODUCTS
```

This makes the frontend usable as a standalone demonstration even when MySQL or PHP is not running.

---

# 🔐 Security

The application uses several basic security mechanisms:

* PHP sessions for authentication
* Password hashing during registration
* PDO for database interaction
* Prepared database queries where implemented
* HTML escaping when displaying account information
* Authentication checks for protected pages
* Foreign key constraints in MySQL

For production deployment, additional security measures should be implemented, including:

* CSRF protection
* Stronger session configuration
* HTTPS
* Secure cookies
* Server-side validation for every input
* Rate limiting
* Production database credentials
* Proper error logging
* Payment gateway security
* Authorization checks for order access

---

# 🧑‍💻 User Workflow

## New Customer

```text
1. Open VANA
2. Browse products
3. Select a product
4. Add product to cart
5. Open cart
6. Proceed to checkout
7. Register an account
8. Log in
9. Enter delivery information
10. Select payment method
11. Place order
```

## Returning Customer

```text
1. Open VANA
2. Log in
3. Browse products
4. Add products to cart
5. Open cart
6. Proceed to checkout
7. Enter/confirm customer information
8. Select payment method
9. Place order
10. Access account
```

---

# 📱 Responsive Design

The frontend is designed for:

* Desktop
* Laptop
* Tablet
* Mobile devices

Responsive styling is primarily contained in:

```text
Frontend/style.css
```

The account page also contains its own responsive CSS rules.

---

# 🖼️ Assets

Product images are stored in:

```text
Frontend/images/
```

Botanical ingredient images are stored in:

```text
Frontend/images/ingredients/
```

These assets are used throughout the product, ingredient, and visual sections of the website.

---

# ⚠️ Current Project Notes

## Product Data Difference

There is currently a difference between the static frontend data and the database.

`Frontend/data.js` contains:

```text
6 products
```

while `DataBase/vana.sql` currently inserts:

```text
4 products
```

The additional frontend products include:

* Neem Clay Mask
* Rose & Vetiver Mist

If the application is intended to use the database as the single source of truth, these products should be added to the SQL database.

---

## Checkout Implementation

There are two order-related PHP files:

```text
backend/checkout.php
backend/place_order.php
```

`place_order.php` should be treated as the preferred implementation when placing an authenticated user's order.

Before production use, the checkout flow should be tested end-to-end with the current database schema.

---

# 🧪 Testing Checklist

Before considering the project complete, test:

* [ ] Database connection
* [ ] User registration
* [ ] Duplicate email handling
* [ ] User login
* [ ] Incorrect password handling
* [ ] Logout
* [ ] Product loading from database
* [ ] Static product fallback
* [ ] Product search
* [ ] Category filtering
* [ ] Product sorting
* [ ] Product detail page
* [ ] Add to cart
* [ ] Remove from cart
* [ ] Change cart quantity
* [ ] Stock validation
* [ ] Cart persistence
* [ ] Checkout form
* [ ] Order creation
* [ ] Stock deduction
* [ ] Account page authentication
* [ ] Mobile responsiveness

---

# 🚧 Future Improvements

The project can be extended with:

* Product reviews and ratings
* Admin dashboard
* Admin product management
* Inventory management
* Coupon and discount system
* Order status tracking
* Email order notifications
* Password reset
* Two-factor authentication
* Payment gateway integration
* Razorpay/Stripe integration
* Customer order history page
* Wishlist
* Advanced product search
* Product recommendations
* Analytics dashboard
* Image upload management

---

# 📌 Project Summary

**VANA — Botanical Apothecary** is a PHP/MySQL-based botanical beauty e-commerce project with a responsive HTML/CSS/JavaScript frontend.

The project demonstrates:

* Full-stack web development
* Authentication
* Session management
* Database design
* Product management
* Shopping cart functionality
* Checkout/order processing
* Responsive UI design
* LocalStorage
* PHP/MySQL integration

It can be run locally using **XAMPP/WAMP + Apache + MySQL** and is suitable as an academic project, portfolio project, or foundation for a production botanical e-commerce application.

---

## 📞 Project Information

**Project Name:** VANA — Botanical Apothecary

**Application Type:** E-Commerce Website

**Frontend:** HTML5, CSS3, JavaScript

**Backend:** PHP

**Database:** MySQL

**Authentication:** PHP Sessions

**Cart Storage:** Browser LocalStorage + database support

**Recommended Local Server:** XAMPP / WAMP

**Database:** `vana_db`
