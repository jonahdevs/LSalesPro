# LSalesPro - B2B Sales Order and Inventory Management API

A comprehensive RESTful API for managing B2B sales workflows including orders, products, inventory, customers, notifications, and dashboard analytics.

---

## 📦 Tech Stack

- **Backend:** Laravel 12
- **Database:** MySQL
- **Caching:** Redis
- **Authentication:** Laravel Sanctum
- **Notifications:** Laravel Notifications
- **Testing:** Postman Collection
- ** Roles and Permissions:** Spatie Roles and permissions


--- 

## Setup Instructions

### 1. Clone the Reposity

```Bash ```
git clone https://github.com/jonahdevs/LSalesPro
cd LSalesPro
---


### 2. Install Dependencies
composer install

---
copy .env.example .env

--- generate key
php artisan key:generate

### 3. Set up database
- Set your database values in the .env file
DB_DATABASE=lsalespro
DB_USERNAME=root
DB_PASSWORD=secret

### 4. Run Migrations and Seeder
php artisan migrate
php artisan db:seed

### 5. Start the Server
- you can start your server using one of the following

php artisan serve
composer run dev


# API MODULES
### Authentication and Users
- Post /api/auth/login ✅
- Post /api/auth/register ✅


## Dashboard Management
- GET /api/dashboard/summary  - 
    -- total sales amount ✅
    -- Number of orders ✅
    -- Average order value ✅
    -- inventory turnover rate 🔃
- put /api/dashboard/sales-perfomance - Sales data with date filtering ✅
- put /api/dashboard/inventory-status - Category-wise inventory summary ✅
- put /api/dashboard/top-products - Top 5 selling products ✅

### Customer Management
- GET /api/customers  - it lists all customer and also it accept filters as query parameters ✅
- GET /api/customers/{id} - single customer details ✅
- POST /api/customers  - Create customer using form Data, ✅
- PUT /api/customers/{id}  - Updates customer details using form Data, ✅
- Delete /api/customers  - Soft Deletes our customer from the system ✅
- POST /api/customers/{id}/orders  - Customer Order history ✅
- Post /api/customers/{id}/credit-status - Customer credit limit and balance  ✅
- Post /api/customers/map-data - Customer locations for mapping ✅


## Inventory Management
- GET /api/products  - it lists all products and also it accept filters as query parameters ✅
- GET /api/products/{id} - single product details ✅
- POST /api/products  - Create product using form Data, ✅
- PUT/PATCH /api/products/{id}  - Updates product details using form Data, ✅
- Delete /api/products  - Soft Deletes product from the system ✅
- GET /api/products/{id}/stock  - Real time stock across warehouse ✅
- POST /api/products/{id}/reserve - Reserve stock for order  ✅
- POST /api/products/{id}/release - release reserved stock  ✅
- GET /api/products/low-stock - products below reorder level ✅


## Sales Order Management
- GET /api/orders  - it lists all orders and also it accept filters as query parameters ✅
- GET /api/orders/{id} - single order details ✅
- POST /api/orders  - Create new order using form Data, ✅
- PUT /api/orders/{id}/status  - Updates order status using form Data, ✅
- GET /api/orders/invoice  - Soft Deletes order from the system 🔃
- POST /api/orders/calculate-total  - Preview order calculations 🔃

## Warehouse Management
- GET /api/warehouses  - it lists all warehouses  ✅
- GET /api/warehouses/{id}/inventory - Warehouse specific inventory ✅
- POST /api/stock-transfers  - Transfer stock between warehouses ✅
- GET /api/stock-transfer  - Transfer history ✅


## Notifications Management
- GET /api/notifications  - it lists all warehouses  ✅
- put /api/notifications/{id}/read - Marks a specific notification as read ✅
- put /ape/notifications/read-all  - Mark all as read ✅
- DELETE /api/notifications/{id}  - Delete specific notification ✅
- GET /api/notifications/unread-count  - Unread count ✅


### Postman collections

