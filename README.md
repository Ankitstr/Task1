# E-Commerce Website

A modern e-commerce website built with PHP, MySQL, and Bootstrap.

## Features

- User Authentication (Login/Register)
- Product Catalog
- Shopping Cart
- User Profile Management
- Admin Dashboard
- Responsive Design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/ecommerce-website.git
```

2. Navigate to the project directory:

```bash
cd ecommerce-website
```

3. Create a database and import the SQL schema:

```bash
mysql -u root -p < database/schema.sql
```

4. Configure the database connection:

- Copy `config/database.example.php` to `config/database.php`
- Update the database credentials in `config/database.php`

5. Start your local server:

```bash
php -S localhost:8000
```

## Project Structure

```
├── assets/          # CSS, JS, and image files
├── auth/            # Authentication related files
├── config/          # Configuration files
├── database/        # Database schema and migrations
├── includes/        # Common PHP includes
├── products/        # Product related files
└── README.md        # Project documentation
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
