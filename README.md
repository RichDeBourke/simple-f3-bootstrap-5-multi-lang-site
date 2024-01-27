A simple, multi-language website using Fat-Free
============================

A frontend / backend setup for a simple, multi-language website using **Bootstrap 5** and the Fat-Free PHP framework (*but no database — this is not a CMS*).

## Overview

Combining [Bootstrap 5](https://getbootstrap.com/docs/5.3/getting-started/introduction/) with the [Fat-Free](https://fatfreeframework.com) php framework to build websites that:

* use templates for assembling the pages
* provide clean URLs
* support automatic language selection
* work on a shared server
* are easy to install (just copy and paste, no package manager required)

Previously, to gain experience with Fat-Free and [Bootstrap 4](https://getbootstrap.com/docs/4.6/getting-started/introduction/), I created a [demo site](http://sbf-testing.byethost7.com/en/home) using Bootstrap 4 and Fat-Free, and shared the files through a [GitHub repository](https://github.com/RichDeBourke/simple-f3-multi-lang-site).

For the release of Bootstrap 5.3, which supports dark mode (and no longer supports Internet Explorer), I created a [new demo](https://sbf-bootstrap5.alwaysdata.net/) site and repository.

## Demo

The code from this repository is in operation at [https://sbf-bootstrap5.alwaysdata.net/](https://sbf-bootstrap5.alwaysdata.net/), running on a [free hosting service](https://www.alwaysdata.com/) that provides PHP and Apache. The intent is for the code to be an as-complete-as-possible package, rather than just a bare-bones starting point for building a site.

*This design is not a CMS. There is no database. All of the content is in the configuration file, the controller files, and the content templates, which works well for static sites.*


## Applying the setup

All of my files for the demo site, Fat-Free configuration, controller, and template files, and the associated SCSS, image, and JavaScript files are in this GitHub repository. The Bootstrap and Fat-Free files are available from [Bootstrap](https://getbootstrap.com/) and [Fat-Free](https://fatfreeframework.com).

It should be possible to replace my site specific content with content for a different website.

## What's included (not a comprehensive list)

#### Fat-Free

* config.ini
* routes.ini
* all of the controller files
* all of the templates for the pages  

*The Fat-Free files are not included – those files are available from [Fat-Free](https://fatfreeframework.com)*

#### Bootstrap

Bootstrap is easy to structure with its available classes and using Sass. There are a few things I changed from the standard Bootstrap code: 

* For Bootstrap's offcanvas menu:
  * Use the navbar toggle-button to both open and close the menu (eliminates the need for a close button on the menu)
  * Automatically close the menu when a same-page link is clicked
  * Added an option for focus to be on the target rather than the menu button – *requires modifying the module.js source file*
* For full-screen background images with modals
  * Added `is-fixed` class to the background static image so it works with modals (Bootstrap has the function, but it's not well documented)

*The Bootstrap files are not included – those files are available from [Bootstrap](https://getbootstrap.com/)*

#### General website stuff

* Dynamic sitemap generation with xhtml:links for alternate languages
* Utilize `preload` and `picture` to provide a selection of hero images (4 smartphone, 4 tablet, and 5 laptop/monitor) for the browser to select from to improve page loading speed
* Uses Fatfree's SMTP plug-in for email messages
* Custom error response pages

#### Multi-language support

While the content for the [demo site](https://sbf-bootstrap5.alwaysdata.net/) is in English, the demo does have Chinese and Korean pages to demonstrate the multi-language operation. The Chinese and Korean home pages were created from the English home page using Google Translate. The remainder of the Chinese and Korean pages, which are provided only to demonstrate the navigation, just have Google translations of the English pages' titles and descriptions.

#### Using system fonts

Font styles use `system-ui,` the same font the operating system uses to display text. This provides a similar look to what the user sees on the system screens, and it speeds up page startup time as there are no fonts to download.

#### Dark mode

Comply with the user's `prefers-color-scheme: dark` setting (when set) using Bootstrap's color mode structure.

## Compatibility

The demo site works with the latest versions of:

* Edge (desktop & Surface)
* Chrome (mobile & desktop)
* Firefox (mobile & desktop)
* Android (Samsung) Internet Browser
* Opera (mobile & desktop)
* Safari (mobile & desktop)

The site does not work with Internet Explorer, other than to show general content. 

## Licensing

This code is provided under an [MIT license](http://opensource.org/licenses/mit-license.php). See the [LICENSE](https://github.com/RichDeBourke/simple-f3-bootstrap-5-multi-lang-site/blob/main/LICENSE) file for details.

Fat-Free is provided under a [GNU Public License (GPL v3)](https://www.gnu.org/licenses/gpl-3.0.html)

Bootstrap is provided under an [MIT license](https://github.com/twbs/bootstrap/blob/master/LICENSE)

## Updates

**2023/07/09** – Initial release for Bootstrap 5.3 version.

**2024/01/25** – Modified navbar toggler and verified use with Bootstrap 5.3.2.
