# Styling Area — Forest Brutalist

| File | Purpose |
|---|---|
| `DESIGN.md` | Full design system spec & rules |
| `tokens.css` | CSS custom properties (colors, fonts, shadows) |

## Quick Rules
- **No rounded corners** (`border-radius: 0` always)
- **Neo-shadow** on all cards & buttons: `4px 4px 0 0 #012d1d`
- **3px solid border** on all structural containers
- **Epilogue Black** for all headings, **Space Grotesk UPPERCASE** for buttons/labels
- Tailwind config → extend with `fontFamily`, `boxShadow`, `colors` from tokens above
