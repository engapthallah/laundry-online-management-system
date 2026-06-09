# Laundry Online Management System (LOMS)

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

A comprehensive, role-based web application designed to streamline and automate laundry shop operations. This system serves as a bridge between laundry customers, shop administrators, processing staff, and delivery drivers, coordinating order processing, payment verification, and logistics.

This project was developed as a **Final Year Software Engineering Thesis Project**.

---

## 🚀 Key Features

* **Multi-Role Dashboards**: Tailored interfaces and permission structures for Admins, Staff, Delivery Drivers, and Customers.
* **Order Lifecycle Management**: Dynamic tracking of orders from pickup requests through cleaning, packing, and final delivery.
* **Integrated Payments System**: Invoice generation, confirmation flow, payment history, and printable customer receipts.
* **Delivery & Logistics Routing**: Dedicated driver panel with assigned pickup/delivery tasks, status updates, and customer address info.
* **Real-time Analytics**: Built-in reports, revenue graphs, order statistics, CSV data exports, and printable PDF reports for managers.
* **Feedback & Reviews**: Public testimonial wall and structured customer order review system.
* **Notification Engine**: Role-specific in-app notifications for order updates, assignments, and ticket replies.
* **Support Ticket Desk**: Complete customer support portal with inquiry forms and admin reply functionality.

---

## 🛠️ Technology Stack

* **Backend Framework**: [Laravel 12](https://laravel.com) (PHP 8.2+)
* **Database**: MySQL / SQLite (for testing)
* **Frontend Structure & Styling**: Blade Templates, [Bootstrap 5](https://getbootstrap.com/), Vanilla CSS
* **Asset Bundling & Javascript**: [Vite](https://vite.dev/), Alpine.js, Tailwind CSS (utility support)
* **Icons & Typography**: Font Awesome 6, Google Fonts (Inter)
* **Testing Suite**: PHPUnit, Laravel Breeze (Auth scaffold)

---

## 📦 System Modules

### 1. Public Portal
* **Home Page**: Informative introduction to services, pricing, and operational details.
* **Reviews Wall**: Public feed of verified customer reviews and star ratings.
* **Contact Support**: Public contact form that links directly into the system's ticketing module.

### 2. Customer Dashboard
* **Order Center**: Request laundry pickups, select services (e.g., wash, dry, dry clean, iron), and track order progress.
* **Payment Hub**: View order invoices, confirm online payments (manually uploading details or retrying), and print transaction receipts.
* **Reviews & Feedback**: Rate completed orders and leave reviews that display on the public site.
* **Support Tickets**: Create and view replies to customer service inquiries.

### 3. Staff Portal
* **Order Processor**: View assigned orders, update laundering progress (e.g., laundering, drying, ironed, ready), and log notes.
* **Notifications**: Track newly assigned tasks in real-time.

### 4. Delivery Driver Panel
* **Logistics Center**: Manage assigned pickups and deliveries, update order location status, and complete tasks with timestamps.
* **Customer Contacts**: Quick access to addresses and contact numbers for deliveries.

### 5. Admin Control Panel
* **Management Console**: Control system users (deactivate/activate), add/modify laundry services, and set unit prices.
* **Order Dispatcher**: Assign orders to staff and delivery drivers.
* **Financial Auditing**: View, verify, fail, or refund orders. Export payment records as CSV files.
* **Customer Service Desk**: Read, reply, close, or export customer support tickets.
* **Analytics Center**: Generate printable reports and download CSV/PDF data tables tracking revenue, order counts, and service popularity.

---

## ⚙️ Installation Instructions

Follow these steps to set up the project locally:

### Prerequisites
* PHP >= 8.2
* Composer
* Node.js & npm
* MySQL Server (e.g., via XAMPP)

### Step-by-Step Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/engapthallah/laundry.git
   cd laundry
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install and Build Frontend Assets**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   Copy the example environment file:
   ```bash
   # On Linux/macOS
   cp .env.example .env
   
   # On Windows (Command Prompt)
   copy .env.example .env
   ```
   Open the `.env` file and update your database and app configurations:
   ```env
   APP_NAME="LOMS"
   APP_ENV=local
   APP_KEY= # Generate in step 5
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=loms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations & Seeders**
   Ensure your MySQL database server is running, then run:
   ```bash
   php artisan migrate --seed
   ```

7. **Compile Frontend Assets**
   * For Development:
     ```bash
     npm run dev
     ```
   * For Production:
     ```bash
     npm run build
     ```

8. **Serve the Application**
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000` in your web browser.

---

## 🧪 Testing

To run the automated PHPUnit test suite:
```bash
php artisan test
```

---

## 🧑‍💻 Author Information

* **Author**: Eng. Apthallah
* **Role**: Software Engineering Student / Lead Developer
* **Purpose**: Software Engineering Final Year Thesis Project
* **Contact**: [GitHub Profile](https://github.com/engapthallah)

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
