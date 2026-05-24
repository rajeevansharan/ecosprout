# EcoSprout Project Walkthrough

I have successfully completed the development of the "EcoSprout" web application. The codebase has been written with a strong focus on simplicity, readability, and fulfilling your assignment rubric requirements.

## What Was Accomplished

1. **Database & Setup**:
   - Created the database script `database/db.sql` with tables for users, plants, orders, order items, and inquiries.
   - Connected via `config/database.php` using simple PDO with prepared statements for security.

2. **Frontend & UI (Bootstrap)**:
   - Built a clean, responsive layout using Bootstrap 5.
   - Centralized layout components in `includes/header.php`, `includes/footer.php`, and `includes/navbar.php`.
   - The UI follows a nature-inspired green-and-white theme.

3. **Public Interface**:
   - **[Home Page](file:///c:/xampp/htdocs/ecosprout-V2/index.php)**: Welcoming hero section and featured plants.
   - **[Catalog & Details](file:///c:/xampp/htdocs/ecosprout-V2/plants.php)**: Users can browse plants, filter by category, and view individual plant details.
   - **[About & Contact](file:///c:/xampp/htdocs/ecosprout-V2/contact.php)**: Essential pages for business info and a functional inquiry form.

4. **Authentication & E-Commerce**:
   - Simple user registration and login system with password hashing.
   - Session-based cart functionality, allowing users to add/remove items.
   - A straightforward checkout process that records orders directly to the database.

5. **Admin Dashboard**:
   - Located at `admin/index.php`.
   - Allows administrators to perform CRUD operations on the plant inventory.
   - Provides views for incoming customer orders, user management, and contact inquiries.

6. **Documentation (As requested per Tasks 01, 03, & Final Report)**:
   - Generated the required markdown files in your artifacts:
     - `assignment_documentation.md` (Task 01)
     - `testing_documentation.md` (Task 03)
     - `README.md` (Final Report)

> [!TIP]
> To get started, you must import the database structure.
> Open PHPMyAdmin, create a database named `ecosprout_db`, and import the SQL code from `database/db.sql`. The SQL file already includes sample data and a default Admin account (`admin@ecosprout.com` / `password`).

Everything is ready for your assignment presentation! Let me know if you need any adjustments or further explanations.
