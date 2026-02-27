# Frontend agent prompt: Create new product

Use this when implementing the flow to create a new product (e.g. after product-not-found from directions-from-text, or from a dedicated “Add product” form).

---

## Endpoint

| Method | URL |
|--------|-----|
| **POST** | **`/api/v1/products`** |

Base URL is your API origin (e.g. `https://api.example.com`). Request must send JSON with `Content-Type: application/json`. If the API uses auth, include the required headers (e.g. Bearer token).

---

## Request body (JSON:API)

The API expects a **JSON:API** document. All product fields go under `data.attributes`.

```json
{
  "data": {
    "type": "products",
    "attributes": {
      "name": "<required>",
      "slug": "<optional>",
      "description": "<optional>"
    }
  }
}
```

### Required attributes

| Attribute | Type | Rules | Notes |
|-----------|------|--------|--------|
| **`name`** | string | Required, max 255 characters | Display name of the product. |

### Optional attributes

| Attribute | Type | Rules | Notes |
|-----------|------|--------|--------|
| **`slug`** | string | Optional, max 255 characters | URL-friendly identifier. If omitted or empty, the server **generates** it from `name` (e.g. "Extra Virgin Olive Oil" → `extra-virgin-olive-oil`). Must be **unique** across products. |
| **`description`** | string | Optional, nullable | Product description. Can be empty string or null. |

### Minimal valid request (only required field)

```json
{
  "data": {
    "type": "products",
    "attributes": {
      "name": "Extra Virgin Olive Oil"
    }
  }
}
```

### Full request (with optional fields)

```json
{
  "data": {
    "type": "products",
    "attributes": {
      "name": "Extra Virgin Olive Oil",
      "slug": "evoo",
      "description": "Cold-pressed, first pressing."
    }
  }
}
```

---

## Success response

- **Status:** `201 Created`
- **Body:** JSON:API document with the created product as `data` (single resource). The resource `id` is the product’s **slug**. Use it for follow-up requests (e.g. `GET /api/v1/products/{slug}`) or when retrying direction-from-text with the new product.

Example shape (attributes may include more fields from the transformer):

```json
{
  "jsonapi": { "version": "1.1" },
  "data": {
    "type": "products",
    "id": "<slug>",
    "attributes": {
      "name": "...",
      "description": "...",
      "created-at": "..."
    },
    "links": {
      "self": "/api/v1/products/<slug>"
    }
  }
}
```

---

## Error responses

1. **Validation (422 Unprocessable Entity)**  
   - **Missing or invalid `name`:** e.g. empty, or longer than 255 characters. Response body contains validation errors (e.g. `errors` array or similar). Show the user which field failed and the message.
   - **Slug already taken:** If you send a `slug` that another product already has, the API returns 422 with a message like “Product with slug '…' already exists.” and a `slug` error. Either omit `slug` (let the server generate from `name`) or choose another slug.

2. **Authentication/Authorization (401/403)**  
   If the API requires auth and the request is unauthenticated or not allowed to create products, you will get 401 or 403. Prompt the user to log in or use an account with permission.

3. **Server error (5xx)**  
   Show a generic “Something went wrong” and optionally suggest retrying.

---

## Summary for the agent

- **Endpoint:** `POST /api/v1/products`
- **Required:** `data.attributes.name` (string, max 255).
- **Optional:** `data.attributes.slug` (string, unique; if not set, generated from `name`), `data.attributes.description` (string, nullable).
- **Success:** 201, body = JSON:API document with created product; `data.id` is the product slug.
- **Errors:** 422 for validation (missing/invalid `name` or duplicate `slug`); 401/403 for auth; 5xx for server errors.

When guiding the user (e.g. after “product not found” from directions-from-text), offer: **search existing products** (`GET /api/v1/products/search?q=...`) or **create a new product** using this endpoint, then retry the flow with the new product slug.
