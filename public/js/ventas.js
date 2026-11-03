(function () {
    "use strict";

    var form = document.querySelector("[data-sale-form]");

    if (!form) {
        return;
    }

    var rows = form.querySelector("[data-sale-rows]");
    var template = document.getElementById("sale-line-template");
    var dataScript = document.getElementById("venta-productos-data");
    var totalOutput = form.querySelector("[data-sale-total]");
    var announcer = form.querySelector("[data-sale-announcer]");

    if (!rows || !template || !dataScript || !totalOutput) {
        return;
    }

    var products = [];

    try {
        products = JSON.parse(dataScript.textContent || "[]");
    } catch (error) {
        products = [];
    }

    var nextIndex = parseInt(rows.getAttribute("data-next-index") || "0", 10);

    if (!Number.isInteger(nextIndex)) {
        nextIndex = rows.children.length;
    }

    function formatMoney(value) {
        var numeric = Number.isFinite(value) ? value : 0;
        var parts = numeric.toFixed(2).split(".");

        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return "S/ " + parts.join(".");
    }

    function getSelectedOption(select) {
        return select.options[select.selectedIndex] || null;
    }

    function getQuantity(input) {
        var quantity = parseInt(input.value, 10);

        if (!Number.isFinite(quantity) || quantity < 0) {
            return 0;
        }

        return quantity;
    }

    function announce(message) {
        if (!announcer) {
            return;
        }

        announcer.textContent = "";
        window.setTimeout(function () {
            announcer.textContent = message;
        }, 40);
    }

    function updateTotal() {
        var total = 0;
        var lines = rows.querySelectorAll("[data-sale-line]");

        lines.forEach(function (row) {
            var select = row.querySelector("[data-product-select]");
            var quantityInput = row.querySelector("[data-quantity]");
            var option = select ? getSelectedOption(select) : null;

            if (!option || !option.value || !quantityInput) {
                return;
            }

            total += Number(option.dataset.price) * getQuantity(quantityInput);
        });

        totalOutput.textContent = formatMoney(total);
    }

    function updateAvailability() {
        var selects = Array.prototype.slice.call(rows.querySelectorAll("[data-product-select]"));
        var counts = {};

        selects.forEach(function (select) {
            if (!select.value) {
                return;
            }

            counts[select.value] = (counts[select.value] || 0) + 1;
        });

        selects.forEach(function (select) {
            Array.prototype.forEach.call(select.options, function (option) {
                if (!option.value) {
                    return;
                }

                var selectedInThisRow = select.value === option.value;
                var usedByAnotherRow = (counts[option.value] || 0) > (selectedInThisRow ? 1 : 0);

                option.disabled = Number(option.dataset.stock) <= 0 || usedByAnotherRow;
            });
        });
    }

    function updateRow(row) {
        if (!row) {
            return;
        }

        var select = row.querySelector("[data-product-select]");
        var quantityInput = row.querySelector("[data-quantity]");

        if (!select || !quantityInput) {
            return;
        }

        var option = getSelectedOption(select);
        var hasProduct = Boolean(option && option.value);
        var price = hasProduct ? Number(option.dataset.price) : 0;
        var stock = hasProduct ? Number(option.dataset.stock) : 0;
        var quantity = getQuantity(quantityInput);
        var unitPriceOutput = row.querySelector("[data-unit-price]");
        var subtotalOutput = row.querySelector("[data-subtotal]");
        var removeButton = row.querySelector("[data-remove-line]");

        if (removeButton) {
            removeButton.setAttribute(
                "aria-label",
                hasProduct
                    ? "Quitar " + option.dataset.nombre + " de la venta"
                    : "Quitar producto de la venta"
            );
        }

        if (unitPriceOutput) {
            unitPriceOutput.textContent = formatMoney(price);
        }

        if (subtotalOutput) {
            subtotalOutput.textContent = formatMoney(price * quantity);
        }

        if (hasProduct) {
            quantityInput.max = String(Math.max(stock, 0));
        } else {
            quantityInput.removeAttribute("max");
        }

        if (hasProduct && quantity > stock) {
            quantityInput.setCustomValidity("Solo hay " + stock + " unidades disponibles.");
        } else {
            quantityInput.setCustomValidity("");
        }

        updateTotal();
    }

    function populateSelect(select) {
        var previous = select.getAttribute("data-selected") || select.value || "";
        var placeholder = document.createElement("option");

        select.innerHTML = "";
        placeholder.value = "";
        placeholder.textContent = "Selecciona un producto";
        select.appendChild(placeholder);

        products.forEach(function (product) {
            var option = document.createElement("option");

            option.value = String(product.id);
            option.textContent = product.nombre + " — " + formatMoney(Number(product.precio)) + " (Stock: " + product.stock + ")";
            option.dataset.price = product.precio;
            option.dataset.stock = product.stock;
            option.dataset.nombre = product.nombre;

            if (Number(product.stock) <= 0) {
                option.disabled = true;
            }

            select.appendChild(option);
        });

        var previousOption = Array.prototype.find.call(select.options, function (option) {
            return option.value === String(previous);
        });

        select.value = previousOption && !previousOption.disabled ? String(previous) : "";
        select.removeAttribute("data-selected");
    }

    function addRow() {
        var currentIndex = nextIndex;
        var markup = template.innerHTML.replace(/__INDEX__/g, String(currentIndex));
        var container = document.createElement("div");

        nextIndex += 1;
        container.innerHTML = markup.trim();

        var row = container.firstElementChild;

        if (!row) {
            return null;
        }

        rows.appendChild(row);
        populateSelect(row.querySelector("[data-product-select]"));
        updateRow(row);
        updateAvailability();

        return row;
    }

    Array.prototype.forEach.call(rows.querySelectorAll("[data-sale-line]"), function (row) {
        populateSelect(row.querySelector("[data-product-select]"));
        updateRow(row);
    });

    updateAvailability();

    form.addEventListener("change", function (event) {
        if (event.target.matches("[data-product-select]")) {
            updateRow(event.target.closest("[data-sale-line]"));
            updateAvailability();
        }
    });

    form.addEventListener("input", function (event) {
        if (event.target.matches("[data-quantity]")) {
            updateRow(event.target.closest("[data-sale-line]"));
        }
    });

    form.addEventListener("click", function (event) {
        var addButton = event.target.closest("[data-add-line]");

        if (addButton && !addButton.disabled) {
            event.preventDefault();

            var newRow = addRow();

            if (newRow) {
                var select = newRow.querySelector("[data-product-select]");

                announce("Se agregó una línea de producto.");

                if (select) {
                    select.focus();
                }
            }

            return;
        }

        var removeButton = event.target.closest("[data-remove-line]");

        if (!removeButton) {
            return;
        }

        event.preventDefault();

        var row = removeButton.closest("[data-sale-line]");
        var removedProduct = null;

        if (row) {
            var removedSelect = row.querySelector("[data-product-select]");
            var removedOption = removedSelect ? getSelectedOption(removedSelect) : null;

            removedProduct = removedOption && removedOption.value ? removedOption.dataset.nombre : null;
            row.remove();
        }

        if (!rows.querySelector("[data-sale-line]")) {
            addRow();
        } else {
            updateTotal();
            updateAvailability();

            var addLineButton = form.querySelector("[data-add-line]");

            if (addLineButton && !addLineButton.disabled) {
                addLineButton.focus();
            }
        }

        announce(
            removedProduct
                ? "Se quitó " + removedProduct + " de la venta."
                : "Se quitó una línea de producto."
        );
    });
})();
