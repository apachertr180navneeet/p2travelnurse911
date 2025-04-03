if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = function (callback, thisArg) {
        thisArg = thisArg || window;
        for (var i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    };
}
if (!Element.prototype.matches) {
    Element.prototype.matches =
        Element.prototype.matchesSelector ||
        Element.prototype.mozMatchesSelector ||
        Element.prototype.msMatchesSelector ||
        Element.prototype.oMatchesSelector ||
        Element.prototype.webkitMatchesSelector ||
        function (s) {
            var matches = (
                    this.document || this.ownerDocument
                ).querySelectorAll(s),
                i = matches.length;
            while (--i >= 0 && matches.item(i) !== this) {}
            return i > -1;
        };
}
if (!Element.prototype.matches) {
    Element.prototype.matches =
        Element.prototype.msMatchesSelector ||
        Element.prototype.webkitMatchesSelector;
}
if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
        var el = this;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}
(function (arr) {
    arr.forEach(function (item) {
        if (item.hasOwnProperty("prepend")) {
            return;
        }
        Object.defineProperty(item, "prepend", {
            configurable: !0,
            enumerable: !0,
            writable: !0,
            value: function prepend() {
                var argArr = Array.prototype.slice.call(arguments),
                    docFrag = document.createDocumentFragment();
                argArr.forEach(function (argItem) {
                    var isNode = argItem instanceof Node;
                    docFrag.appendChild(
                        isNode
                            ? argItem
                            : document.createTextNode(String(argItem))
                    );
                });
                this.insertBefore(docFrag, this.firstChild);
            },
        });
    });
})([Element.prototype, Document.prototype, DocumentFragment.prototype]);
(function (arr) {
    arr.forEach(function (item) {
        if (item.hasOwnProperty("append")) {
            return;
        }
        Object.defineProperty(item, "append", {
            configurable: !0,
            enumerable: !0,
            writable: !0,
            value: function append() {
                var argArr = Array.prototype.slice.call(arguments),
                    docFrag = document.createDocumentFragment();
                argArr.forEach(function (argItem) {
                    var isNode = argItem instanceof Node;
                    docFrag.appendChild(
                        isNode
                            ? argItem
                            : document.createTextNode(String(argItem))
                    );
                });
                this.appendChild(docFrag);
            },
        });
    });
})([Element.prototype, Document.prototype, DocumentFragment.prototype]);
(function (arr) {
    arr.forEach(function (item) {
        if (item.hasOwnProperty("before")) {
            return;
        }
        Object.defineProperty(item, "before", {
            configurable: !0,
            enumerable: !0,
            writable: !0,
            value: function before() {
                var argArr = Array.prototype.slice.call(arguments),
                    docFrag = document.createDocumentFragment();
                argArr.forEach(function (argItem) {
                    var isNode = argItem instanceof Node;
                    docFrag.appendChild(
                        isNode
                            ? argItem
                            : document.createTextNode(String(argItem))
                    );
                });
                this.parentNode.insertBefore(docFrag, this);
            },
        });
    });
})([Element.prototype, CharacterData.prototype, DocumentType.prototype]);
(function (arr) {
    arr.forEach(function (item) {
        if (item.hasOwnProperty("remove")) {
            return;
        }
        Object.defineProperty(item, "remove", {
            configurable: !0,
            enumerable: !0,
            writable: !0,
            value: function remove() {
                if (this.parentNode !== null) this.parentNode.removeChild(this);
            },
        });
    });
})([Element.prototype, CharacterData.prototype, DocumentType.prototype]);
