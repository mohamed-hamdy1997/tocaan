# E-commerce Backend API

A Laravel-based backend for an e-commerce platform, featuring user authentication, order management, and a payment system.

## Features

- **User Authentication**: Secure JWT-based authentication.
- **Order Management**: Create and track orders with items.
- **Payment System**: Simulated payment processing.

## Tech Stack

- **Framework**: Laravel 12.x
- **Authentication**: JWT
- **Database**: MySQL / SQLite

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/mohamed-hamdy1997/tocaan.git
   cd tocaan
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Start the Server**:
   ```bash
   php artisan serve
   ```

## API Documentation

### Postman Collection
Import `Ecommerce_Backend_API.postman_collection.json` into Postman.
- The `Login` request saves the `access_token` to a collection variable named `token`.
- All protected routes use this `Bearer` token.

### Authentication
| Endpoint | Method | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `/api/auth/register` | `POST` | Register a new user | No |
| `/api/auth/login` | `POST` | Login and get JWT token | No |
| `/api/auth/me` | `POST` | Get authenticated user info | Yes |
| `/api/auth/logout` | `POST` | Invalidate current token | Yes |

### Orders
| Endpoint | Method | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `/api/orders` | `GET` | List all user orders | Yes |
| `/api/orders` | `POST` | Create a new order | Yes |
| `/api/orders/{id}` | `GET` | Get specific order details | Yes |

#### Create Order Request Example:
```json
{
    "items": [
        {
            "product_name": "Laptop",
            "quantity": 1,
            "price": 1200
        }
    ]
}
```

### Payments
| Endpoint | Method | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `/api/payments` | `POST` | Process payment for an order | Yes |

#### Process Payment Request Example:
```json
{
    "order_id": 1,
    "payment_method": "paypal" 
}
```
*Supported methods: `paypal`, `credit_card`*

## Testing

```bash
php artisan test
```

## License
MIT
