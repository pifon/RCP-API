# Frontend agent prompt: Use directions-from-text API

Use this when implementing the recipe direction flow so the frontend sends direction text to the API and uses the created directions.

---

**Context:** Recipe creation has a “general” part (title, cuisine, etc.) and then directions. We want: user types a direction sentence → frontend parses it with `App\Services\DirectionParser` (same logic as API) and shows a preview of how it will be understood → when the user accepts, the frontend sends the **direction string** to the API. The API re-parses with the same parser and creates all direction resources (steps, procedures, servings, ingredients list, creator-only original text).

**Your tasks:**

1. **Keep DirectionParser in sync**  
   The API has a copy at `api/app/Services/DirectionParser.php`. The frontend has `foodbook/app/Services/DirectionParser.php`. Both must stay identical (same parsing logic) so the preview and the API result match. When updating parsing rules, update both files.

2. **Direction creation flow**
   - After the user types a direction sentence and accepts the preview (how it will be understood), call the **from-text** endpoint instead of building and sending structured direction objects.
   - **Endpoint:** `POST /api/v1/recipes/{recipeSlug}/directions/from-text`
   - **Request body (JSON:API):**
     ```json
     {
       "data": {
         "type": "directions",
         "attributes": {
           "direction-text": "<the exact string the user accepted>"
         }
       }
     }
     ```
   - **Response (201):** `data` is an **array** of direction resources (one sentence can become multiple steps). Use `meta.count` and `meta.prep-time-minutes` as needed. Append or merge the returned directions into the current recipe’s direction list in the UI.

3. **Errors**
   - If the API returns **422** with a message that no steps could be parsed, show the user that the text couldn’t be interpreted and ask them to rephrase or use the structured form.
   - If the API returns **404** for a product (parsed ingredient not found), the response will include links to search products and create a product. Show the user that the ingredient “X” wasn’t found and offer: search for a product, or create a new product, then retry (or use the structured direction form with the chosen product).

4. **Optional: structured fallback**  
   Keep the possibility to add a single direction via **POST** `/api/v1/recipes/{slug}/directions` with structured attributes (action, product-slug, measure-slug, amount, duration-minutes, notes, original-text) for power users or when from-text fails.

5. **Original text**  
   The API stores the submitted direction string as a creator-only note on the first created step. When the recipe author views directions, they see this so they can check that the parsed step is correct. No frontend change required except to rely on the API’s `notes` (and any creator-only behaviour) when displaying.

Implement the above so that: (a) the frontend uses the same DirectionParser as the API and shows a preview; (b) on accept, it sends the direction string to `POST .../directions/from-text` and updates the UI from the returned direction list; (c) parser file is kept in sync between frontend and API; (d) product-not-found is handled using the API’s links.
