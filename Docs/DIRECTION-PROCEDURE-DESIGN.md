# Direction vs Procedure: One Step, Multiple Ingredients

## Your example

**Sentence:** "In a large mixing bowl, mix together 1/2 cup each olive oil, white wine, and orange juice."

**Desired:** One direction step that represents three applications of "mix" (0.5 cup olive oil, 0.5 cup white wine, 0.5 cup orange juice).

## Current model (no schema change needed)

We **do not** need one procedure per ingredient. The model already supports this:

| Concept | Role |
|--------|------|
| **Direction** | One step in the recipe (e.g. "Step 3: Mix together …") |
| **Procedure** | The **operation** for that step: (operation = "mix", duration = null). One per direction. |
| **DirectionIngredient** | Each (ingredient + **serving**) = one "mix 0.5 cup X" in that step. Multiple per direction. |

So for your sentence we store:

- **1 Direction** (one step)
- **1 Procedure** (operation = "mix", serving = null when there are multiple ingredients; see below)
- **3 DirectionIngredients**  
  - (ingredient = olive oil, serving = 0.5 cup)  
  - (ingredient = white wine, serving = 0.5 cup)  
  - (ingredient = orange juice, serving = 0.5 cup)

Display: *"Step 1: Mix together 0.5 cup olive oil, 0.5 cup white wine, 0.5 cup orange juice."*

So the "three procedures" you have in mind are the **three direction_ingredients** (each with its own serving). The **one** procedure is just the shared operation ("mix").

## Reuse

- **Procedure** stays **reusable**: (operation, serving, duration). We do **not** add `direction_id` on procedures. Reuse is at the level of "same operation + same serving + same duration" → same procedure row.
- **Serving** is reusable: (product, measure, amount).
- **Direction** and **DirectionIngredient** are the recipe/step-specific part: which step, which ingredients, which amounts.

So "reuse ends" at **servings** and **procedures**; directions and direction_ingredients are the place where we say "in this step we use these ingredients in these amounts."

## Why not Direction 1:N Procedure?

If we made one direction link to **multiple** procedures, we’d have:

- Procedure 1: (mix, 0.5 cup olive oil)  
- Procedure 2: (mix, 0.5 cup white wine)  
- Procedure 3: (mix, 0.5 cup orange juice)

That would duplicate the operation "mix" and blur the idea of procedure as a reusable (operation, serving, duration). We’d also need a join table (direction_id, procedure_id, sequence) and more complex prep-time and display logic.

The current design keeps:

- **One procedure per direction** = one operation (and optional duration) for the step.
- **Multiple direction_ingredients** = the different (ingredient + amount) pairs in that step.

So the best approach is: **keep Direction 1:1 Procedure** and use **DirectionIngredient** for the multiple (ingredient, amount) pairs. No new tables or Direction 1:N Procedure.

## Procedure vs direction_ingredients: who holds the serving(s)

- **Single-ingredient step:** Procedure may have `serving_id` set (the one step amount), and there is one `direction_ingredient` row with that same serving. Optionally we can leave procedure.serving = null and only use direction_ingredients for consistency.
- **Multi-ingredient step:** Procedure has **no** serving: `procedure.serving_id = null`. The step’s amounts are **only** in **direction_ingredients**: each row has `direction_id`, `ingredient_id`, and `serving_id` (the step amount for that ingredient). So procedure is never “linked” to multiple servings; those servings are linked only via direction_ingredients to the direction.

So the difference: a procedure using multiple servings is **not** linked to any serving; those servings are linked via **direction_ingredients** to the direction.

## Optional tweak in code

When a direction has **multiple** ingredients, we set **procedure.serving = null** so the procedure is not tied to any serving. Implemented in `DirectionCreationService`: when `count($ingredientSpecs) > 1`, pass `null` for the procedure’s serving.
