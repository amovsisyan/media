/**
 * simple helper for getting Laravel CSRF Token
 * @returns {string}
 */
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
}

/**
 * helps to handle toast after bad or OK response
 * @param response
 * @param status
 * @param text
 */
function handleResponseToast (response, status, text) {
    var _html = '',
        style = '';
    if (status) {
        _html = text;
        style = 'status_ok';
    } else {
        if (response.response && response.type) {
            var errors = response.response;

            _html = response.type + ': ';
            errors.forEach(function (element, index, array) {
                _html += element;
            });
        } else {
            _html = 'Something Was Wrong'
        }
        style = 'status_warning';
    }
    Materialize.toast(_html, 5000, 'rounded');
    var toasts = document.getElementById("toast-container").getElementsByClassName("toast"),
        toast = toasts[toasts.length-1];

    toast.classList.add(style);
}

/**
 * helps to explode string and returns last from exploded
 * @param string
 * @param separator
 * @returns {T}
 */
function explodeGetLast(string, separator) {
    return string.split(separator).pop();
}

/**
 * update buttons
 * for false, add disable for btns array
 * for true, remove disabled from btns array
 * @param add
 */
function updateAddConfirmButtons(btns, add) {
    if (add) {
        Array.prototype.forEach.call(btns, (function (element, index, array) {
            element.classList.add('disabled');
        }));
    } else {
        Array.prototype.forEach.call(btns, (function (element, index, array) {
            element.classList.remove('disabled');
        }));
    }
}

/**
 * generate Image size Warning if image sizes doesn't in standards
 * @param files
 * @param standards
 */
function imageSizeWarning(files, standards) {
    if (files.length) {
        var dmsRatio = getPostMainImageStandardRatio(standards),
            imgRatio = 0,
            fr = new FileReader;
        fr.onload = function() {
            var img = new Image;
            img.onload = function() {
                imgRatio = img.width / img.height;
                var biggerThanBottom = imgRatio >= dmsRatio.ratioBottom,
                    smallerThanTop = imgRatio <= dmsRatio.ratioTop;

                if (!(biggerThanBottom && smallerThanTop)) {
                    response = {
                        response: ['Size Ratio must be from '
                        + Math.round(dmsRatio.ratioBottom * 100)/100 +
                        ' to ' + Math.round(dmsRatio.ratioTop * 100)/100],
                        type: 'Image size Warning'
                    };
                    handleResponseToast(response, false);
                } else {
                    response = {
                        response: ['Good'],
                        type: 'Correct'
                    };
                    var text = 'Good';
                    handleResponseToast(response, true, text);
                }
            };
            img.src = fr.result;
        };
        fr.readAsDataURL(files[0]);
    }
}

/**
 * generate Ratio for comparison via standards
 * @param standards
 * @returns {{ratioBottom: number, ratioTop: number}}
 */
function getPostMainImageStandardRatio(standards) {
    var stdW = standards.width,
        stdH = standards.height,
        stdRation = stdW / stdH,
        stdDiverg = standards.diverg,
        diverg = stdRation * stdDiverg;

    return {
        ratioBottom: stdRation - diverg,
        ratioTop: stdRation + diverg
    }
}

/**
 * Check does the element have class
 * @param element
 * @param cls
 * @returns {boolean}
 */
function hasClass(element, cls) {
    return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
}