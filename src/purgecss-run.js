const { PurgeCSS } = require('purgecss');
const fs = require('fs');
const path = require('path');

(async () => {
  try {
    const result = await new PurgeCSS().purge({
      content: ['./app/ui/**/*.html'],
      css: ['./css/style.min.css'],
      safelist: {
        standard: [
          'd-none',
          'd-sm-inline',
          'active'
        ],
        deep: [/^dropdown-menu-md-end/, /^carousel/, /^d-(none|sm-inline)$/, /^navbar/, /^small-logo/, /^flag-icon-/]
      }
    });

    const outPath = path.resolve(__dirname, './css/style.purged.css');
    fs.writeFileSync(outPath, result[0].css, 'utf-8');
    console.log(`✅ Purged CSS written to ${outPath}`);
  } catch (error) {
    console.error('❌ PurgeCSS failed:', error);
    process.exit(1);
  }
})();
