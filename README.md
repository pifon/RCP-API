# Pifon API

Pifon API (pifon.co.uk/api) is a RESTful backend-for-frontend (BFF) designed to power the applications running on pifon.com and foodbook.uk. It provides a robust backend to manage user-generated recipes, ingredients, and cuisines.

## Features

- **Monetization (Future Feature):** Some recipes may be marked for monetization in the future.
- **User Authentication:** Only registered users can create, edit, or delete recipes.
- **Recipe Management:** Users can create, update, and delete recipes for various dishes.
- **Cuisines:** Recipes are linked to specific cuisines for better organization.
- **Ingredient Handling:**
    - Recipes reuse predefined ingredients.
    - Users can create new ingredients if they are not already present in the system.
- **Preparation Steps:** Ingredients are used in preparation steps to guide the execution of the dish creation process.

## Future Features

- **Editable Recipes:** Users will be able to "borrow" a recipe as a base, alter it, and publish it as their own. The new recipe will give credit to the original creator, and if the new recipe is monetized, a payment will be made to the original creator.
- **Recipe Collections:** Users will be able to create and manage collections of recipes for specific occasions or themes.
- **Favorites:** Users can mark their favorite recipes for quick access.
- **Saved Recipes by Cuisine:** Users can save recipes from specific cuisines, allowing for easier browsing and organization.
- **Recipe Rating:** Users will be able to rate recipes with stars or a similar system.
- **Private Rating:** Users can leave a private rating and comment on a recipe, visible only to the creator of the recipe.
- **Public Ranking:** Popular recipes will be ranked publicly, with separate rankings for individual cuisines.
- **Pricing for Recipes:** Recipes will include a price, potentially based on ingredients and serving size.
- **Calories and Nutrition Information:** Recipes will include detailed information on calories and other nutritional values.
- **"Play" Quick or Real-Time Recipe:** A feature to guide users through the cooking process, either as a quick overview or in real-time, assisting them step-by-step as they cook.
- **API Access for 3rd Parties:** Third-party developers will be able to access the API for their own applications or integrations.
- **Account with Sub-Accounts:** A main account can create and manage sub-accounts. Sub-accounts will have restricted access, only able to view the recipes linked to the main account. This is particularly useful for training within a restaurant or kitchen setting.
- **Test of Knowledge for Restaurants:** A feature for restaurants to create tests based on their recipes, enabling new staff to demonstrate their knowledge of the restaurant's dishes.
- **Test of Chef's Knowledge:** A feature to test a chef’s knowledge, such as identifying which dishes can be made from a given set of ingredients, estimating cooking times for specific recipes, and understanding the staples of various cuisines.
- **Shopping List Tracking:** Track what ingredients are currently available in the user's fridge and offer suggestions for dishes that can be cooked with the existing ingredients. It will also suggest additional ingredients to purchase to make other recipes.
- **Weekly/Monthly Menu Planning:** Users will be able to plan their meals for the week or month, considering dietary requirements, budget constraints, preferences, and ingredients already purchased. The system will suggest recipes that align with these factors.

- Unlimited number of recipes for paying users.
- Additional features for paying users.

## Technology Stack

- **Laravel:** The backend is built using the Laravel framework.
- **Docker:** The backend code is encapsulated within a Docker container.
- **MySQL:** A separate (but persistent) MySQL database is used for data storage.

## Documentation

Full API documentation, including examples of endpoints, is available in the `/documentation` folder.

## Installation & Setup

To run the backend locally, follow these steps:

```bash
git clone https://gitlab.pifon.co.uk/pifon/api.git
cd api
composer install
php artisan migrate --seed
php artisan serve
```

## License

This project is licensed under the MIT License.

For more details, visit [pifon.co.uk/api](https://pifon.co.uk/api).

---

This should now include your feature for planning weekly or monthly menus. Let me know if you need further tweaks!