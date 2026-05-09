# Design System: Forest Brutalist

### 1. Overview & Creative North Star

**Creative North Star: The Organic Brutalist**
Forest Brutalist is a design system that marries the raw, unapologetic structure of Neo-Brutalism with the refined palette of an artisan culinary brand. It rejects the "soft" web of the 2010s in favor of heavy borders, high-contrast typography, and intentional asymmetry. The system is designed to feel "built" rather than "rendered," echoing the structural integrity of high-end bakery goods.

### 2. Colors

The palette is rooted in a deep, evergreen primary (`#012d1d`) and a vibrant, acidic tertiary (`#D4EF70`).

- **The "No-Line" Rule Exception:** Unlike standard Material systems, Forest Brutalist _mandates_ the use of heavy 3px solid borders in the primary color for all structural containers. However, _internal_ sectioning within cards should still follow tonal transitions.
- **Surface Hierarchy:**
  - `surface_container_lowest` (#ffffff) is reserved for interactive cards.
  - `surface_container_low` and `secondary_container` are used for large section backgrounds to create a rhythmic shift between "warm" and "cool" zones.
- **Glassmorphism:** Navigation headers use a 80% opacity blur (`#f4fbf7/80`) to maintain context while scrolling.

### 3. Typography

The system uses a tri-font approach to differentiate brand voice, utility, and content.

- **Display & Headlines (Epilogue):** Set in Extra Bold/Black weights with tight tracking (`tracking-tighter`). Large scales like `6rem` (96px) and `3.75rem` (60px) dominate the visual hierarchy.
- **Body (Manrope):** Optimized for readability in sizes `1.125rem` (18px) to `1.25rem` (20px).
- **Labels & Utility (Space Grotesk):** Used for buttons, tags, and navigation. Always uppercase with generous letter spacing (`tracking-widest`) to provide a technical, modern contrast to the organic headline font.

### 4. Elevation & Depth

**The Hard-Shadow Principle**
Forest Brutalist rejects ambient, soft shadows in favor of "Neo-Shadows"—solid, offset blocks of color.

- **Standard Elevation:** `4px 4px 0 0 #012d1d`. Used for standard cards and buttons.
- **Active/Hover Elevation:** `6px 6px 0 0 #012d1d`. Used to indicate interactive states.
- **Layering Principle:** Depth is achieved by "tilting" elements (e.g., 2-degree rotations) and stacking containers with heavy borders.
- **The Ghost Border:** When a primary border is too heavy, use `outline_variant` at 3px width rather than reducing opacity.

### 5. Components

- **Buttons:** Rectangular, 0px border-radius, 3px border, with a Neo-Shadow. Text is always uppercase Space Grotesk.
- **Product Cards:** White background (`surface_container_lowest`), 3px border, and a 4px Neo-Shadow. Images inside cards must also have a 3px border.
- **Chips/Tags:** Small, rectangular labels with 1px borders, using `tertiary_fixed` for high-priority callouts (e.g., "Bestseller").
- **Inputs:** High contrast, 3px border, with `primary_fixed` focus states.

### 6. Do's and Don'ts

**Do:**

- Use intentional rotation (1-3 degrees) on images and testimonial blocks to break the grid.
- Apply `backdrop-blur-md` on fixed elements to maintain depth.
- Use the Primary color for all structural lines.

**Don't:**

- Use rounded corners. Every element should have a `roundedness: 0` setting.
- Use soft, feathered shadows.
- Use "Standard" font weights for headlines; always opt for Bold or Black.
- Section off content using 1px grey lines; use 3px Primary lines or background shifts.
