---
name: bootstrap-frontend
description: Use when: bootstrapping a new front-end project with Bootstrap. Provides a quick checklist to set up HTML, CSS, JS structure and integrate Bootstrap framework.
---

# Bootstrap Front End Skill

This skill guides you through setting up a new front-end project using Bootstrap.

## Quick Checklist

1. **Create Project Structure**
   - Create `index.html` in the root
   - Create `css/` directory for custom styles
   - Create `js/` directory for custom scripts
   - Create `assets/` directory for images, fonts, etc.

2. **Integrate Bootstrap**
   - Add Bootstrap CSS link in `<head>`: `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">`
   - Add Bootstrap JS before closing `</body>`: `<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>`

3. **Set Up Basic HTML Template**
   - Use Bootstrap's starter template or create a basic HTML5 structure
   - Include a container div with Bootstrap classes

4. **Add Custom Files**
   - Create `css/custom.css` for overrides
   - Create `js/custom.js` for custom functionality
   - Link them in the HTML

5. **Test the Setup**
   - Open `index.html` in a browser
   - Verify Bootstrap components work (e.g., add a button with `btn btn-primary`)

This checklist assumes using Bootstrap via CDN. For npm installation, additional steps would be needed.