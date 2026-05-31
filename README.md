# EcoSprout - Plant Nursery Web Application

## About The Project
EcoSprout is a simple, beginner-friendly web application built for a plant nursery and gardening services company. It allows customers to browse a catalog of plants, add items to their shopping cart, and place orders. It also includes an Admin Dashboard where the store owner can manage the plant inventory, view customer orders, and read messages sent through the contact form.

This project was built focusing on simplicity, clean code, and easy explainability for university assignments.

### Built With
- **Frontend**: HTML5, CSS3, Bootstrap 5 (for simple, responsive layouts)
- **Backend**: Core PHP (Procedural style)
- **Database**: MySQL (using PDO for secure, prepared statements)
- **Environment**: XAMPP

---

## Features
- **Public Pages**: Home, About, Contact, Plant Catalog, and detailed Plant views.
- **Customer Features**: User registration, login, session-based shopping cart, and checkout.
- **Admin Features**: Secure admin dashboard, add/edit/delete plants, view incoming customer orders, and view contact inquiries.

---

## Getting Started (How to Run the Project)

Follow these simple steps to get the project running on your local machine.

### Prerequisites
- **XAMPP** (or any similar local server software like WAMP/MAMP) must be installed.

### Installation Steps

1. **Move the Project Folder:**
   Make sure this entire project folder (`ecosprout-V2`) is placed inside your XAMPP `htdocs` directory. 
   - Windows path: `C:\xampp\htdocs\ecosprout-V2`

2. **Start XAMPP:**
   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

3. **Set Up the Database:**
   - Open your web browser and go to: `http://localhost/phpmyadmin`
   - Click on **Databases** at the top.
   - Create a new database and name it exactly: `ecosprout_db`
   - Click on your newly created `ecosprout_db` database on the left sidebar.
   - Click on the **Import** tab at the top.
   - Click **Choose File** and select the `db.sql` file located inside the `database/` folder of this project (`ecosprout-V2/database/db.sql`).
   - Scroll down and click the **Import** (or **Go**) button.
   *This script will automatically create all the necessary tables and insert sample plants and a default Admin account.*

4. **Run the Application:**
   - Open your web browser and go to: `http://localhost/ecosprout`
   - You should now see the EcoSprout home page!

---

## Admin Access
To test the admin features, use the default administrator account that was created during the database import:
- **Login URL**: Click "Login" on the top right of the website.
- **Email**: `admin@ecosprout.com`
- **Password**: `password`

*Once logged in as an admin, you will be redirected to the secure Admin Dashboard.*

---

## Folder Structure Explained
- `admin/` - Contains all the protected pages for the admin dashboard.
- `assets/` - Contains custom CSS styling, images, and JavaScript.
- `config/` - Contains `database.php` which handles the connection to MySQL.
- `database/` - Contains the `db.sql` file used to set up the database.
- `includes/` - Contains reusable layout parts (header, footer, navigation bar).
- Main directory (`*.php`) - Contains the public-facing pages (index, about, contact, etc.).
