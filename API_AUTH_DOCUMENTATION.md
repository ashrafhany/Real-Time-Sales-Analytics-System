# API Documentation

## Authentication Endpoints

### Register a new user
```
POST /api/auth/register
```
**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
**Response:**
```json
{
  "message": "User registered successfully",
  "user": { user object },
  "access_token": "token_string",
  "token_type": "Bearer"
}
```

### Login
```
POST /api/auth/login
```
**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "message": "Login successful",
  "user": { user object },
  "access_token": "token_string",
  "token_type": "Bearer"
}
```

### Get User Info
```
GET /api/user
```
**Headers:**
```
Authorization: Bearer {your_token}
```
**Response:**
```json
{
  "user": { user object }
}
```

### Logout
```
POST /api/auth/logout
```
**Headers:**
```
Authorization: Bearer {your_token}
```
**Response:**
```json
{
  "message": "Logged out successfully"
}
```

## Orders Endpoints

### Get User Orders
```
GET /api/orders
```
**Headers:**
```
Authorization: Bearer {your_token}
```
**Response:**
```json
{
  "orders": [
    { order objects with products }
  ]
}
```

### Create New Order
```
POST /api/orders
```
**Headers:**
```
Authorization: Bearer {your_token}
```
**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 5
}
```
**Response:**
```json
{
  "message": "Order created successfully",
  "order": { order object with product }
}
```

### Get Specific Order
```
GET /api/orders/{id}
```
**Headers:**
```
Authorization: Bearer {your_token}
```
**Response:**
```json
{
  "order": { order object with product }
}
```

## Testing the API

You can test these endpoints using tools like:
1. Postman
2. cURL
3. JavaScript fetch API
4. axios

Example with cURL:

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get User Info (with token)
curl http://localhost:8000/api/user \
  -H "Authorization: Bearer {your_token}"

# Create Order (with token)
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -d '{"product_id":1,"quantity":5}'
```

Remember to replace `{your_token}` with the actual token received from login or registration.
