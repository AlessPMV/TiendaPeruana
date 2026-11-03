(function () {
    "use strict";

    var toggle = document.querySelector("[data-nav-toggle]");
    var nav = document.querySelector("[data-nav]");

    if (!toggle || !nav) {
        return;
    }

    var label = toggle.querySelector(".nav-toggle__label");
    var desktopQuery = window.matchMedia("(min-width: 861px)");

    function setOpen(isOpen) {
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        toggle.setAttribute("aria-label", isOpen ? "Cerrar menú de navegación" : "Abrir menú de navegación");
        nav.classList.toggle("is-open", isOpen);

        if (label) {
            label.textContent = isOpen ? "Cerrar" : "Menú";
        }
    }

    function closeMenu() {
        if (toggle.getAttribute("aria-expanded") === "true") {
            setOpen(false);
            return true;
        }

        return false;
    }

    toggle.addEventListener("click", function () {
        setOpen(toggle.getAttribute("aria-expanded") !== "true");
    });

    nav.addEventListener("click", function (event) {
        if (event.target.closest("a")) {
            closeMenu();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && closeMenu()) {
            toggle.focus();
        }
    });

    document.addEventListener("click", function (event) {
        if (!nav.classList.contains("is-open")) {
            return;
        }

        if (!nav.contains(event.target) && !toggle.contains(event.target)) {
            closeMenu();
        }
    });

    function handleDesktopChange(event) {
        if (event.matches) {
            setOpen(false);
        }
    }

    if (typeof desktopQuery.addEventListener === "function") {
        desktopQuery.addEventListener("change", handleDesktopChange);
    } else {
        desktopQuery.addListener(handleDesktopChange);
    }

    setOpen(false);
})();

(function () {
    "use strict";

    var forms = document.querySelectorAll("form[data-confirm]");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            var message = form.getAttribute("data-confirm");

            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
})();

(function () {
    "use strict";

    var toggle = document.querySelector("[data-a11y-toggle]");
    var panel = document.querySelector("[data-a11y-panel]");

    if (!toggle || !panel) {
        return;
    }

    var closeButton = panel.querySelector("[data-a11y-close]");
    var decreaseButton = panel.querySelector("[data-a11y-text-decrease]");
    var increaseButton = panel.querySelector("[data-a11y-text-increase]");
    var textOutput = panel.querySelector("[data-a11y-text-output]");
    var contrastToggle = panel.querySelector("[data-a11y-contrast-toggle]");
    var resetButton = panel.querySelector("[data-a11y-reset]");
    var status = panel.querySelector("[data-a11y-status]");
    var root = document.documentElement;
    var lastFocused = null;
    var levels = {
        "-2": 85,
        "-1": 92,
        "0": 100,
        "1": 110,
        "2": 120
    };

    function store(key, value) {
        try {
            if (value === null) {
                localStorage.removeItem(key);
            } else {
                localStorage.setItem(key, value);
            }
        } catch (error) {}
    }

    function announce(message) {
        if (!status) {
            return;
        }

        status.textContent = "";
        window.setTimeout(function () {
            status.textContent = message;
        }, 40);
    }

    function getLevel() {
        var value = parseInt(root.getAttribute("data-a11y-text"), 10);

        return Object.prototype.hasOwnProperty.call(levels, String(value)) ? value : 0;
    }

    function setTextLevel(level, notify) {
        if (!Object.prototype.hasOwnProperty.call(levels, String(level))) {
            level = 0;
        }

        root.setAttribute("data-a11y-text", String(level));
        textOutput.textContent = String(levels[String(level)]) + "%";
        store("tienda-a11y-text", String(level));

        if (notify) {
            announce("Tamaño del texto al " + levels[String(level)] + "%.");
        }
    }

    function isHighContrast() {
        return root.getAttribute("data-a11y-contrast") === "high";
    }

    function setHighContrast(enabled, notify) {
        if (enabled) {
            root.setAttribute("data-a11y-contrast", "high");
        } else {
            root.removeAttribute("data-a11y-contrast");
        }

        contrastToggle.setAttribute("aria-pressed", enabled ? "true" : "false");
        store("tienda-a11y-contrast", enabled ? "1" : null);

        if (notify) {
            announce(enabled ? "Modo oscuro de alto contraste activado." : "Modo oscuro de alto contraste desactivado.");
        }
    }

    function openPanel() {
        if (!panel.hidden) {
            return;
        }

        lastFocused = document.activeElement;
        panel.hidden = false;
        toggle.setAttribute("aria-expanded", "true");
        toggle.setAttribute("aria-label", "Cerrar panel de accesibilidad");

        if (closeButton) {
            closeButton.focus();
        }
    }

    function closePanel(restoreFocus) {
        if (panel.hidden) {
            return;
        }

        panel.hidden = true;
        toggle.setAttribute("aria-expanded", "false");
        toggle.setAttribute("aria-label", "Abrir panel de accesibilidad");

        if (restoreFocus) {
            if (lastFocused && typeof lastFocused.focus === "function") {
                lastFocused.focus();
            } else {
                toggle.focus();
            }
        }
    }

    setTextLevel(getLevel(), false);
    setHighContrast(isHighContrast(), false);

    toggle.addEventListener("click", function (event) {
        event.stopPropagation();

        if (panel.hidden) {
            openPanel();
        } else {
            closePanel(true);
        }
    });

    if (closeButton) {
        closeButton.addEventListener("click", function () {
            closePanel(true);
        });
    }

    if (decreaseButton) {
        decreaseButton.addEventListener("click", function () {
            setTextLevel(Math.max(-2, getLevel() - 1), true);
        });
    }

    if (increaseButton) {
        increaseButton.addEventListener("click", function () {
            setTextLevel(Math.min(2, getLevel() + 1), true);
        });
    }

    if (contrastToggle) {
        contrastToggle.addEventListener("click", function () {
            setHighContrast(!isHighContrast(), true);
        });
    }

    if (resetButton) {
        resetButton.addEventListener("click", function () {
            setTextLevel(0, false);
            setHighContrast(false, false);
            store("tienda-a11y-text", null);
            store("tienda-a11y-contrast", null);
            announce("Ajustes de accesibilidad restablecidos.");
        });
    }

    var focusableSelector = "a[href], button:not([disabled]), input:not([disabled]):not([type='hidden']), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex='-1'])";

    function getFocusable() {
        return Array.prototype.filter.call(panel.querySelectorAll(focusableSelector), function (element) {
            return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
        });
    }

    document.addEventListener("keydown", function (event) {
        if (panel.hidden) {
            return;
        }

        if (event.key === "Escape") {
            event.preventDefault();
            closePanel(true);
            return;
        }

        if (event.key !== "Tab") {
            return;
        }

        var focusable = getFocusable();

        if (focusable.length === 0) {
            event.preventDefault();
            return;
        }

        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        var active = document.activeElement;
        var outside = !panel.contains(active);

        if (event.shiftKey) {
            if (active === first || outside) {
                event.preventDefault();
                last.focus();
            }
        } else if (active === last || outside) {
            event.preventDefault();
            first.focus();
        }
    });

    document.addEventListener("click", function (event) {
        if (panel.hidden) {
            return;
        }

        if (!panel.contains(event.target) && !toggle.contains(event.target)) {
            closePanel(false);
        }
    });
})();
