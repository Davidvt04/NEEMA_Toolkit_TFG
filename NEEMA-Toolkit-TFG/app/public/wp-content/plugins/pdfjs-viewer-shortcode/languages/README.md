# Translation Support

This plugin is translation-ready with the text domain `pdfjs-viewer-shortcode`.

## For Plugin Users

### Using Loco Translate (Recommended)

1. Install the [Loco Translate](https://wordpress.org/plugins/loco-translate/) plugin
2. Go to **Loco Translate → Plugins**
3. Select "PDFjs Viewer - Embed PDFs"
4. Click "New language" and select your language
5. Translate the strings and save

### Using WPML or Polylang

These plugins will automatically detect the translation strings and allow you to translate them through their interfaces.

## For Developers

### Updating the POT File

The POT (Portable Object Template) file contains all translatable strings from the plugin. To regenerate it after adding new translatable strings:

**If you have WP-CLI installed:**

```bash
wp i18n make-pot . languages/pdfjs-viewer-shortcode.pot --domain=pdfjs-viewer-shortcode --exclude=node_modules,vendor
```

**Using npm script:**

```bash
npm run makepot
```

Note: The npm script requires WP-CLI to be installed globally.

### Creating a Translation

1. Copy `languages/pdfjs-viewer-shortcode.pot` to `languages/pdfjs-viewer-shortcode-{locale}.po`

    - Example: `pdfjs-viewer-shortcode-es_ES.po` for Spanish (Spain)
    - Example: `pdfjs-viewer-shortcode-fr_FR.po` for French (France)

2. Use a tool like [Poedit](https://poedit.net/) to translate the strings

3. Poedit will automatically generate the `.mo` file (compiled translation)

4. Place both `.po` and `.mo` files in the `languages/` directory

### Language Codes

Use WordPress locale codes. Common examples:

-   Spanish (Spain): `es_ES`
-   French (France): `fr_FR`
-   German: `de_DE`
-   Italian: `it_IT`
-   Portuguese (Brazil): `pt_BR`
-   Japanese: `ja`

Full list: https://translate.wordpress.org/

## Contributing Translations

To contribute a translation:

1. Fork the repository
2. Create your translation files in the `languages/` directory
3. Submit a pull request

Or share your `.po` and `.mo` files via a GitHub issue.
