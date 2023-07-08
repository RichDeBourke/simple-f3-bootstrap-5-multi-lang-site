/*  JavaScript functions for the Site Construction website (for Bootstrap 5)
Copyright 2018-2022 Rich DeBourke
Code may be used under an MIT License:
https://github.com/RichDeBourke/simple-f3-bootstrap-5-multi-lang-site/blob/main/LICENSE */

/* Use ES6 - import, rather than require (commonjs) style */

// const {Offcanvas, Modal} = require("bootstrap");
// import {
//     Offcanvas,
//     Modal
// } from "bootstrap";

import Modal from "bootstrap/js/src/modal";
import Collapse from "bootstrap/js/src/collapse";


window.addEventListener("load", function() {
    let scrollTimer = null;

    function scrollHandler() {
        const scrollTop = window.pageYOffset;
        const windowHeight = window.innerHeight;
        const scrollToTop = document.getElementById("scroll-to-top");

        if (scrollTop > 100) {
            document.body.classList.add("small-logo");
        } else {
            document.body.classList.remove("small-logo");
        }

        if (scrollTop > windowHeight) {
            scrollToTop.style.opacity = 1;
        } else {
            scrollToTop.style.opacity = 0;
        }
    }
    // Ignore scroll events as long as a scrollHandler execution is in the queue
    // Queue gets processed every 33ms (30fps)
    function scrollThrottler() {
        if (!scrollTimer) {
            scrollTimer = window.setTimeout(function() {
                scrollTimer = null;
                scrollHandler();
            }, 33);
        }
    }

    window.addEventListener("scroll", scrollThrottler, {
        passive: true
    });
    // Mobile devices will fire a resize event when the URL bar is hidden or shown
    window.addEventListener("resize", scrollThrottler);
    // Call scrollThrottler one time to be sure the page is adjusted if the page is not at the top
    scrollThrottler();

    // Show the EU cookie modal (if it's the first time to the site or a new session)
    if (document.cookie.indexOf("eu-cookie-notice") < 0) { // no cookie
        if (window.sessionStorage.getItem("eu-opt-out") !== "true") { // no session item
            //Cookie not found and no current session, so show the modal
            const options = {
                "backdrop": "static",
                "keyboard": false
            };
            const euModal = new Modal(document.getElementById("euCookieModal"), options);

            // Show the modal on every page except the terms and error pages
            if (document.body.dataset.scPageType !== "terms" && document.body.dataset.scPageType !== "error") {
                euModal.show();
            }

            document.getElementById("btn-cookie-accept").addEventListener("click", function() {
                let date = new Date();
                date.setTime(date.getTime() + 31536000000);
                document.cookie = "eu-cookie-notice=true; expires=" + date.toGMTString() + ";path=/";
                euModal.hide();
            }, {
                passive: true
            });

            document.getElementById("btn-cookie-opt-out").addEventListener("click", function() {
                try {
                    window.sessionStorage.setItem("eu-opt-out", "true");
                } catch (error) {
                    console.error(error);
                }
                euModal.hide();
            }, {
                passive: true
            });
        }
    }

    // On scroll-to-top, set the focus
    document.getElementById("scroll-to-top").addEventListener("click", function() {
        window.setTimeout(() => {
            document.documentElement.tabIndex = 0;
            document.documentElement.focus();
        }, 1000);
    }, {
        passive: true
    });

    // Display an overlay when changing to a new URL
    const externalLinks = document.querySelectorAll("a[href^='http']");
    const overlay = document.querySelector("#overlay");

    externalLinks.forEach((link) => {
        link.addEventListener("click", function(event) {
            overlay.classList.add("show");
        });
    });

    if (document.body.dataset.scPageType === "contact") {
        window.bsCollapse = new Collapse('#collapseAlert', {
            toggle: false
        });
    }
});