# SecretOfBeauty

SecretOfBeauty is a full-stack beauty and appointment management web application developed using PHP, MySQL, HTML, CSS, and JavaScript.

The system combines appointment scheduling, beauty product shopping, personalized beauty diagnosis, user profile management, and administration tools in one platform.

## Features

- User registration and login
- User profile management
- Appointment booking
- Appointment history
- Appointment cancellation
- Waiting list management
- Hair, skin, and nails diagnosis
- Personalized beauty recommendations
- Hair, cosmetics, and nails product categories
- Shopping cart
- Order management
- Product search
- Admin dashboard
- User management
- Product management
- Inventory and low-stock monitoring
- Email notifications
- Employee management
- Business and branch management
- FAQ-based customer support chat

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- PHPMailer
- Git
- GitHub

## Main Modules

### Authentication
- User registration
- Login
- Role-based access
- Admin and employee accounts
- User profile management

### Appointment Management
- Book appointments
- Select business, location, date, and time
- Appointment history
- Appointment cancellation
- Waiting list support

### Beauty Diagnosis

The platform includes diagnosis forms for:

- Hair
- Skin
- Nails

The system uses the user's answers to provide personalized beauty and product recommendations.

### Online Store

The application includes an online store with separate product categories:

- Hair products
- Cosmetics
- Nail products

Users can:

- Search for products
- Add products to cart
- Update quantities
- Place orders
- View order history

### Admin Dashboard

Administrators can manage:

- Users
- Products
- Appointments
- Orders
- Feedback
- Stock levels
- Business information

The dashboard also displays system statistics.

### Inventory Monitoring

The system automatically checks product quantities.

When product stock becomes low, an email notification can be sent to the administrator.

### Email Notifications

PHPMailer is used for automated email notifications such as:

- Appointment confirmation
- Appointment cancellation
- Waiting list registration
- Employee notifications
- Low-stock alerts

Sensitive email credentials are stored outside the public repository.

### Customer Support Chat

The project includes a rule-based FAQ support chat that provides answers to common questions about:

- Beauty treatments
- Hair products
- Cosmetics
- Appointments
- Delivery

## Security Improvements

The project uses:

- Prepared statements for database queries
- Password hashing
- `password_verify()` for login
- Session-based authentication
- Protected email credentials
- `.gitignore` for sensitive files

## Project Structure

Some of the main project files include:

- `index.php` – Login
- `signUp.php` – User registration
- `homePage.php` – Main page
- `appointment.php` – Appointment booking
- `appointment_history.php` – Appointment history
- `cart.php` – Shopping cart
- `confirm_order.php` – Order confirmation
- `admin_products.php` – Product administration
- `manage_users.php` – User administration
- `hair_diagnosis.php` – Hair diagnosis
- `skin_diagnosis.php` – Skin diagnosis
- `nails_diagnosis.php` – Nail diagnosis
- `PROstock.php` – Stock monitoring
- `mail.php` – Email functions
- `connectToDB.php` – Database connection

## Installation

1. Install XAMPP or another PHP/MySQL environment.
2. Place the project inside the `htdocs` folder.
3. Start Apache and MySQL.
4. Create a MySQL database named:

```text
user
