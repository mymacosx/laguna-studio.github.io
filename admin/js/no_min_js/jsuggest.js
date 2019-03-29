(function($) {
    $.suggest = function(input, options) {
        var $input = $(input).attr("autocomplete", "off");
        var $results = $(document.createElement("ul"));
        var timeout = false;
        var prevLength = 0;
        var cache = [];
        var cacheSize = 0;
        $results.addClass(options.resultsClass).appendTo('body');
        resetPosition();
        $(window).load(resetPosition).resize(resetPosition);
        $input.blur(function() {
            setTimeout(function() {
                $results.hide();
            },
            200);
        });
        try {
            $results.bgiframe();
        } catch(e) {}
        if ($.browser.mozilla) $input.keypress(processKey);
        else $input.keydown(processKey);
        function resetPosition() {
            var offset = $input.offset();
            $results.css({
                top: (offset.top + input.offsetHeight) + 'px',
                left: offset.left + 'px'
            });
        }
        function processKey(e) {
            if ((/27$|38$|40$/.test(e.keyCode) && $results.is(':visible')) || (/^13$|^9$/.test(e.keyCode) && getCurrentResult())) {
                if (e.preventDefault) e.preventDefault();
                if (e.stopPropagation) e.stopPropagation();
                e.cancelBubble = true;
                e.returnValue = false;
                switch (e.keyCode) {
                case 38:
                    prevResult();
                    break;
                case 40:
                    nextResult();
                    break;
                case 9:
                case 13:
                    selectCurrentResult();
                    break;
                case 27:
                    $results.hide();
                    break
                }
            } else if ($input.val().length != prevLength) {
                if (timeout) clearTimeout(timeout);
                timeout = setTimeout(suggest, options.delay);
                prevLength = $input.val().length;
            }
        }
        function suggest() {
            var q = $.trim($input.val());
            if (q.length >= options.minchars) {
                cached = checkCache(q);
                if (cached) {
                    displayItems(cached['items'])
                } else {
                    $.get(options.source, {
                        q: q
                    },
                    function(txt) {
                        $results.hide();
                        var items = parseTxt(txt, q);
                        displayItems(items);
                        addToCache(q, items, txt.length);
                    });
                }
            } else {
                $results.hide();
            }
        }
        function checkCache(q) {
            for (var i = 0; i < cache.length; i++) if (cache[i]['q'] == q) {
                cache.unshift(cache.splice(i, 1)[0]);
                return cache[0];
            }
            return false;
        }
        function addToCache(q, items, size) {
            while (cache.length && (cacheSize + size > options.maxCacheSize)) {
                var cached = cache.pop();
                cacheSize -= cached['size'];
            }
            cache.push({
                q: q,
                size: size,
                items: items
            });
            cacheSize += size;
        }
        function displayItems(items) {
            if (!items) return;
            if (!items.length) {
                $results.hide();
                return;
            }
            var html = '';
            for (var i = 0; i < items.length; i++) html += '<li>' + items[i] + '</li>';
            $results.html(html).show();
            $results.children('li').mouseover(function() {
                $results.children('li').removeClass(options.selectClass);
                $(this).addClass(options.selectClass);
            }).click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                selectCurrentResult();
            });
        }
        function parseTxt(txt, q) {
            var items = [];
            var tokens = txt.split(options.delimiter);
            for (var i = 0; i < tokens.length; i++) {
                var token = $.trim(tokens[i]);
                if (token) {
                    token = token.replace(new RegExp(q, 'ig'),
                    function(q) {
                        return '<span class="' + options.matchClass + '">' + q + '</span>';
                    });
                    items[items.length] = token;
                }
            }
            return items;
        }
        function getCurrentResult() {
            if (!$results.is(':visible')) return false;
            var $currentResult = $results.children('li.' + options.selectClass);
            if (!$currentResult.length) $currentResult = false;
            return $currentResult;
        }
        function selectCurrentResult() {
            $currentResult = getCurrentResult();
            if ($currentResult) {
                $input.val($currentResult.text());
                $results.hide();
                if (options.onSelect) options.onSelect.apply($input[0]);
            }
        }
        function nextResult() {
            $currentResult = getCurrentResult();
            if ($currentResult) $currentResult.removeClass(options.selectClass).next().addClass(options.selectClass);
            else $results.children('li:first-child').addClass(options.selectClass);
        }
        function prevResult() {
            $currentResult = getCurrentResult();
            if ($currentResult) $currentResult.removeClass(options.selectClass).prev().addClass(options.selectClass);
            else $results.children('li:last-child').addClass(options.selectClass);
        }
    };
    $.fn.suggest = function(source, options) {
        if (!source) return;
        options = options || {};
        options.source = source;
        options.delay = options.delay || 100;
        options.resultsClass = options.resultsClass || 'ac_results';
        options.selectClass = options.selectClass || 'ac_over';
        options.matchClass = options.matchClass || 'ac_match';
        options.minchars = options.minchars || 2;
        options.delimiter = options.delimiter || '\n';
        options.onSelect = options.onSelect || false;
        options.maxCacheSize = options.maxCacheSize || 65536;
        this.each(function() {
            new $.suggest(this, options);
        });
        return this;
    };
})(jQuery);