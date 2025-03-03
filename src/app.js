const application = document.getElementById("application");
const preloadTracker = {
    application: false,
    hero: false,
};

const preloadHandler = () => {
    if (preloadTracker.application && preloadTracker.hero)
        document.body.classList.add("loaded");
};

application?.addEventListener("loaded", () => {
    console.log("[OAG Website]", "Application loaded");
    preloadTracker.application = true;
    preloadHandler();
});

const heroPreload = new Image();
heroPreload.src =
    "https://data.ortsarchiv-gemeinlebarn.org/ueberland/hero_15SG-1399_3032-cut@2540.jpg";
heroPreload.addEventListener("load", () => {
    console.log("[OAG Website]", "Hero Image loaded");
    preloadTracker.hero = true;
    preloadHandler();
});

/**
 * Components
 */

import { defineCustomElements } from "@ortsarchiv-gemeinlebarn/components/dist/components/index.js";
defineCustomElements();
