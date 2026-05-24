# EcoSprout Plant Nursery - Final Report

## 1. Introduction
"EcoSprout" is a web-based plant nursery and gardening services platform designed for a newly established company in Kegalle, Sri Lanka. The platform allows customers to browse, purchase, and learn about plants, while providing administrators with a robust system to manage inventory and orders.

## 2. Objectives
- To develop a simple, responsive, and beginner-friendly web application.
- To implement core e-commerce functionalities without complex third-party APIs.
- To fulfill the requirements of the university assignment rubric.

## 3. Requirement Analysis
**Functional Requirements:**
- User registration and authentication.
- Product catalog with search and filter capabilities.
- Shopping cart and checkout system.
- Admin dashboard for CRUD operations on plants, orders, and inquiries.

**Non-Functional Requirements:**
- Simple, intuitive user interface (UI).
- Responsive design (Mobile-friendly).
- Secure database interactions (Prepared Statements).

## 4. Similar Website Analysis
We analyzed popular plant nursery websites to determine best practices for design. We found that utilizing a clean white background with vibrant green accents (Bootstrap's success color) creates an inviting, nature-focused aesthetic. The navigation structure was kept linear to ensure ease of use for beginners. *(See `assignment_documentation.md` for details).*

## 5. Sitemap
- Home
- Plants Catalog -> Plant Details
- About
- Contact
- Login / Register
- Cart -> Checkout
- Admin Dashboard (Manage Plants, Orders, Inquiries, Users)

## 6. Database Design
The system uses a simple MySQL relational database (`ecosprout_db`) with five tables:
1. `users`: Stores admin and customer credentials.
2. `plants`: Stores plant inventory details.
3. `orders`: Tracks customer orders.
4. `order_items`: Maps plants to specific orders.
5. `inquiries`: Stores messages from the contact form.

## 7. Frontend Development
The frontend was developed using **HTML5, CSS3, and Bootstrap 5**. Bootstrap was chosen for its grid system and pre-styled components, which allowed for the rapid development of a clean and responsive UI. Custom CSS was used sparingly to maintain simplicity.

## 8. Backend Development
The backend is powered by **Procedural PHP** (and simple PDO for database connections). Prepared statements were strictly used for all database interactions to ensure security against SQL injection. Sessions were utilized to manage state for the shopping cart and user authentication.

## 9. Authentication
A role-based authentication system was implemented.
- **Customers** can register, log in, manage their cart, and place orders.
- **Administrators** (role = 'admin') have access to a protected dashboard to manage the store. Passwords are securely hashed using PHP's `password_hash()` function.

## 10. Testing
Manual testing was conducted across all core modules (Auth, Catalog, Cart, Checkout, Admin). All critical test cases passed successfully. *(See `testing_documentation.md` for the full test plan).*

## 11. Challenges
- **Simplicity vs. Functionality**: Balancing the need for a fully functional e-commerce flow while keeping the codebase extremely simple and beginner-friendly was a primary challenge. This was resolved by using a session-based cart instead of a complex database-driven cart for active sessions.
- **Security**: Ensuring that even a simple application is safe from basic attacks required the strict implementation of PDO prepared statements.

## 12. Conclusion
The "EcoSprout" project successfully meets all the criteria set out in the assignment brief. It demonstrates a solid understanding of fundamental web development concepts including frontend design, backend logic, database management, and security, all wrapped in a clean, easily explainable package.

## 13. References
- Bootstrap Documentation: https://getbootstrap.com/docs/5.3/
- PHP Documentation: https://www.php.net/docs.php
