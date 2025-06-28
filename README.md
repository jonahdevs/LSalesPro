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


---
### 2. Install Dependencies
composer install

---
copy .env.example .env

--- generate key
php artisan key:generate

---

### 3. Set up database
- Set your database values in the .env file
DB_DATABASE=lsalespro
DB_USERNAME=root
DB_PASSWORD=secret

---

### 4. Run Migrations and Seeder
php artisan migrate
php artisan db:seed

### 5. Start the Server
- you can start your server using one of the following

php artisan serve
composer run dev

----


----

# API Authentication
## Authentication and Users
All API routes (except login / register) are protected using **Laravel Sanctum**

### To Authenticate
   ** You can import this [Postman Collection](./postman/Authentication.postman_collection.json) to test the API endpoints

1. Send a POST request to `/api/auth/login` with email and password
  -- for testing use 'admin@example.com' as email and 'password' as password
2. You'll receive a token in the response
    ```
    {
    "success": "",
    "message": "",
    "data": {
        "user": {},
        "token": "XXXXXXXXXXXXXXXXXXXXXXX"
    }
    ```
3. Include this token  in the `Authorization' header
    ```
    Authorization: Bearer your_access_toke
    ```
    NOTE:: All subsequest requests must include this toke

    - In Authentication you can perform actions such as refresh token, login, logout, forgot password and password reset.
    - All the authetication routes start with api/auth
     * /api/auth/login
     * /api/auth/logout
     * /api/auth/refresh
     * /api/auth/user
     * /api/auth/password/forgot
     * /api/auth/password/reset
----


----


## Dashboard Management
  ** You can import this [Postman Collection](./postman/DashboardAnalytics.postman_collection.json) to test the API endpoints

    - The Dashboard Api provides real-time business intelligence insighrs to help sales manager, inventory controllers, and admins track and analyze key performance metrics across the platform

* GET /api/dashboard/summary
    - Returns a summary of key perfomance indicatorss 
    - example response json
        {
            "total_sales": "93844.00",
            "order_count": 1,
            "average_order_value": 93844,
            "inventory_turnover_rate": 0.06
        }
* GET /api/dashboard/saes-perfomance
    - Returns a filtered breakdown of sales over time, ideal for graphing trends.
    -example response json 
        {
            "labels": [
                "2025-06-28"
            ],
            "data": [
                "93844.00"
            ]
        }
* GET /api/dashboard/inventory-summary
    - Returns a summary of inventory grouped by product category, helping teams assess stock distribution.
    - example response json
        [
            {
                "category": "Mineral Oils",
                "available_stock": 98,
                "products_count": 1
            },
            {
                "category": "Synthetic Oils",
                "available_stock": 145,
                "products_count": 1
            }
        ]
* GET  /api/dashboard/top-products
    - Returns the top 5 selling products based on quantity sold within a time range.
    - example response json
        [
            {
                "name": "SuperFuel Max 20W-50",
                "sold": "12"
            },
            {
                "name": "EcoDrive Synthetic 5W-30",
                "sold": "5"
            }
        ]

NOTE:: The data being controller can be filtered using a date range default is one month

---- 


----

## Customer Management
  ** You can import this [Postman Collection](./postman/CustomerManagement.postman_collection.json) to test the API endpoints


* GET /api/customers  
    - it lists all customer and also it accept filters as query parameters 
    - example response json 
        {
                "data": [
                    {
                        "id": "1",
                        "attributes": {
                            "name": "",
                            "email": "",
                            "phone": "",
                            ***
                        }
                    },
                ],
                "links": {
                    "first": "xxxxxxxxxxxxxxxxxxxxx",
                    "last": "xxxxxxxxxxxxxxxxxxxxx",
                    "prev": null,
                    "next": null
                },
                "meta": {
                    "current_page": 1,
                    "from": 1,
                    "last_page": 1,
                    "links": [
                        {
                            "url": null,
                            "label": "&laquo; Previous",
                            "active": false
                        },
                        {
                            "url": "xxxxxxxxxxxxxxxxxxxxx",
                            "label": "1",
                            "active": true
                        },
                        {
                            "url": null,
                            "label": "Next &raquo;",
                            "active": false
                        }
                    ],
                    "path": "xxxxxxxxxxxxxxxxxxxxxxxxx",
                    "per_page": 10,
                    "to": 2,
                    "total": 2
                }
            }

* GET /api/customers/{id} 
    - single customer details 
    - example response json
     {
        "data": {
            "id": "1",
            "attributes": {
                "name": "",
                "email": "i",
                "phone": "",
            }
        }
    }


* POST /api/customers  
    - Create customer using form Data, 
* PUT /api/customers/{id}  
    - Updates customer details using form Data, 
* Delete /api/customers  
    - Soft Deletes our customer from the system 
* POST /api/customers/{id}/orders  
    - Customer Order history 
* Post /api/customers/{id}/credit-status 
    - Customer credit limit and balance  
* Post /api/customers/map-data 
    - Customer locations for mapping 

----


----

## Inventory Management
  ** You can import this [Postman Collection](./postman/InventoryManagement.postman_collection.json) to test the API endpoints


* GET /api/products  
    - it lists all products and also it accept filters as query parameters 
* GET /api/products/{id} 
    - single product details 
* POST /api/products  
    - Create product using form Data, 
* PUT/PATCH /api/products/{id}  
    - Updates product details using form Data, 
* Delete /api/products  
    - Soft Deletes product from the system 
* GET /api/products/{id}/stock  
    - Real time stock across warehouse 
* POST /api/products/{id}/reserve 
    - Reserve stock for order  
* POST /api/products/{id}/release 
    - release reserved stock  
* GET /api/products/low-stock 
    - products below reorder level 

----

----

## Sales Order Management
  ** You can import this [Postman Collection](./postman/SaleOrderManagement.postman_collection.json) to test the API endpoints

* GET /api/orders  
    - it lists all orders and also it accept filters as query parameters 
* GET /api/orders/{id} 
    - single order details 
* POST /api/orders  
    - Create new order using form Data, 
* PUT /api/orders/{id}/status  
    - Updates order status using form Data, 
* GET /api/orders/invoice  
    - Soft Deletes order from the system 🔃
* POST /api/orders/calculate-total  
    - Preview order calculations 🔃


----

----

## Warehouse Management
  ** You can import this [Postman Collection](./postman/WarehouseManagement.postman_collection.json) to test the API endpoints

* GET /api/warehouses  
    - it lists all warehouses  
* GET /api/warehouses/{id}/inventory 
    - Warehouse specific inventory 
* POST /api/stock-transfers  
    - Transfer stock between warehouses 
* GET /api/stock-transfer  
    - Transfer history 


----


----

## Notifications Management
  ** You can import this [Postman Collection](./postman/NotificationApi.postman_collection.json) to test the API endpoints


* GET /api/notifications  
    - it lists all warehouses  
* put /api/notifications/{id}/read 
    - Marks a specific notification as read 
* put /ape/notifications/read-all  
    - Mark all as read 
* DELETE /api/notifications/{id}  
    - Delete specific notification 
* GET /api/notifications/unread-count  
    - Unread count 

----


----
### System Activities
  ** You can import this [Postman Collection](./postman/SystemActivities.postman_collection.json) to test the API endpoints

* Get /api/system-activities
    - dumps all activities happening in our system

----
