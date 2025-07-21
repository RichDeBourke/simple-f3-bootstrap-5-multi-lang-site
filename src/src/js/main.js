/*!
 * --------------------------------------------------------------------------
 * Bootstrap offcanvas.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
 * --------------------------------------------------------------------------
 */
// Entry point for Webpack
// this file is only used for Webpack to create the bundled
// JavaScript file

// main.js

// Import Bootstrap components from bootstrap-components.js
import * as Bootstrap from './bootstrap-components.js';

// Import site-specific code
import './site-js-functions.js';

// Re-export Bootstrap components (if needed)
export default Bootstrap;
