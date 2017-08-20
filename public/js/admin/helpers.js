function getCSRFToken() {
    var metaTags = document.getElementsByTagName("meta");
    var CSRFToken = "";
    for (var i = 0; i < metaTags.length; i++) {
        if (metaTags[i].getAttribute("name") == "csrf-token") {
            CSRFToken = metaTags[i].getAttribute("content");
            return CSRFToken;
        }
    }
    return CSRFToken;
}

/**
 * Get the closest matching element up the DOM tree.
 * @private
 * @param  {Element} elem     Starting element
 * @param  {String}  selector Selector to match against
 * @return {Boolean|Element}  Returns null if not match found
 */
function getClosest (elem, selector) {
    // Element.matches() polyfill
    if (!Element.prototype.matches) {
        Element.prototype.matches =
            Element.prototype.matchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector ||
            Element.prototype.oMatchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            function(s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                    i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {}
                return i > -1;
            };
    }
    // Get closest match
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
        if ( elem.matches( selector ) ) return elem;
    }

    return null;
};

function handleResponseToast (status, text, style) {
    Materialize.toast(text, 5000, 'rounded');
    var toasts = document.getElementById("toast-container").getElementsByClassName("toast "),
        toast = toasts[toasts.length-1];

    toast.classList.add(style);
}