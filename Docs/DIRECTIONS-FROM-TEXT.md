# Directions from text – API contract

## Flow

1. User types a direction sentence in the frontend (e.g. *"In a bowl, mix 1/2 cup olive oil with 2 tbsp lemon juice"*).
2. Frontend parses it with the **same** `DirectionParser` (kept in sync with `api/app/Services/DirectionParser.php`) and shows the user how it will be understood (action, ingredients, duration).
3. When the user accepts, the frontend sends the **raw direction string** to the API.
4. The API re-parses with the same parser, creates one or more direction resources (procedure, serving, ingredient linking, creator-only original text), and returns the created directions.

## Endpoint

**POST** `/api/v1/recipes/{slug}/directions/from-text`

- **Auth:** Required (recipe must exist; user must be able to add directions – typically recipe owner).
- **Content-Type:** `application/vnd.api+json`
- **Body (JSON:API):**

```json
{
  "data": {
    "type": "directions",
    "attributes": {
      "direction-text": "In a large bowl, mix 1/2 cup olive oil with 2 tbsp lemon juice for 1 minute."
    }
  }
}
```

- **Success (201):** JSON:API document with:
  - `data`: array of created direction resources (same shape as GET `/recipes/{slug}/directions`).
  - `meta.count`: number of directions created (one sentence can become multiple steps).
  - `meta.prep-time-minutes`: recipe prep time after adding.

- **Errors:**
  - **404** – Recipe not found.
  - **422** – Validation:
    - `direction-text` missing or empty.
    - No steps could be parsed from the text.
    - Product not found (parsed ingredient name → slug not in DB): use `ProductNotFoundException` links (`products-search`, `create-product`) to let the user search or create the product.

## Parser sync

- Parser lives in **API:** `api/app/Services/DirectionParser.php`.
- Frontend should keep **Foodbook** `app/Services/DirectionParser.php` in sync with the API copy (same logic) so preview and API parse match.
- Parsed steps are mapped to API as: `type` → action, `duration` → duration-minutes, first `ingredients[0]` → product (by slug from `name`), `quantity.amount` / `quantity.unit` → amount and measure slug. Product is resolved by slug from the ingredient name; if not found, the API returns 404 with product search/create links.

## Existing endpoint (unchanged)

**POST** `/api/v1/recipes/{slug}/directions` – still available for creating a **single** direction from structured attributes (action, product-slug, measure-slug, amount, duration-minutes, notes, original-text). Use this when the client already has structured data; use **from-text** when the user has accepted a parsed sentence.
