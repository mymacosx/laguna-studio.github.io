(function(C) {
    C.ui = C.ui || {};
    C.extend(C.ui, {plugin: {add: function(F, G, I) {
                var H = C.ui[F].prototype;
                for (var E in I) {
                    H.plugins[E] = H.plugins[E] || [];
                    H.plugins[E].push([G, I[E]])
                }
            }, call: function(E, G, F) {
                var I = E.plugins[G];
                if (!I) {
                    return
                }
                for (var H = 0; H < I.length; H++) {
                    if (E.options[I[H][0]]) {
                        I[H][1].apply(E.element, F)
                    }
                }
            }}, cssCache: {}, css: function(E) {
            if (C.ui.cssCache[E]) {
                return C.ui.cssCache[E]
            }
            var F = C('<div class="ui-resizable-gen">').addClass(E).css({position: "absolute", top: "-5000px", left: "-5000px", display: "block"}).appendTo("body");
            C.ui.cssCache[E] = !!((!(/auto|default/).test(F.css("cursor")) || (/^[1-9]/).test(F.css("height")) || (/^[1-9]/).test(F.css("width")) || !(/none/).test(F.css("backgroundImage")) || !(/transparent|rgba\(0, 0, 0, 0\)/).test(F.css("backgroundColor"))));
            try {
                C("body").get(0).removeChild(F.get(0))
            } catch (G) {
            }
            return C.ui.cssCache[E]
        }, disableSelection: function(E) {
            E.unselectable = "on";
            E.onselectstart = function() {
                return false
            };
            if (E.style) {
                E.style.MozUserSelect = "none"
            }
        }, enableSelection: function(E) {
            E.unselectable = "off";
            E.onselectstart = function() {
                return true
            };
            if (E.style) {
                E.style.MozUserSelect = ""
            }
        }, hasScroll: function(H, F) {
            var E = /top/.test(F || "top") ? "scrollTop" : "scrollLeft", G = false;
            if (H[E] > 0) {
                return true
            }
            H[E] = 1;
            G = H[E] > 0 ? true : false;
            H[E] = 0;
            return G
        }});
    var B = C.fn.remove;
    C.fn.remove = function() {
        C("*", this).add(this).trigger("remove");
        return B.apply(this, arguments)
    };
    function A(F, G, H) {
        var E = C[F][G].getter || [];
        E = (typeof E == "string" ? E.split(/,?\s+/) : E);
        return(C.inArray(H, E) != -1)
    }
    var D = {init: function() {
        }, destroy: function() {
        }, getData: function(F, E) {
            return this.options[E]
        }, setData: function(G, E, F) {
            this.options[E] = F
        }, enable: function() {
            this.setData(null, "disabled", false)
        }, disable: function() {
            this.setData(null, "disabled", true)
        }};
    C.widget = function(F, E) {
        var G = F.split(".")[0];
        F = F.split(".")[1];
        C.fn[F] = function(K, L) {
            var I = (typeof K == "string"), J = arguments;
            if (I && A(G, F, K)) {
                var H = C.data(this[0], F);
                return(H ? H[K](L) : undefined)
            }
            return this.each(function() {
                var M = C.data(this, F);
                if (!M) {
                    C.data(this, F, new C[G][F](this, K))
                } else {
                    if (I) {
                        M[K].apply(M, C.makeArray(J).slice(1))
                    }
                }
            })
        };
        C[G][F] = function(J, I) {
            var H = this;
            this.options = C.extend({}, C[G][F].defaults, I);
            this.element = C(J).bind("setData." + F, function(M, K, L) {
                return H.setData(M, K, L)
            }).bind("getData." + F, function(L, K) {
                return H.getData(L, K)
            }).bind("remove", function() {
                return H.destroy()
            });
            this.init()
        };
        C[G][F].prototype = C.extend({}, D, E)
    };
    C.widget("ui.mouse", {init: function() {
            var E = this;
            this.element.bind("mousedown.mouse", function() {
                return E.click.apply(E, arguments)
            }).bind("mouseup.mouse", function() {
                (E.timer && clearInterval(E.timer))
            }).bind("click.mouse", function() {
                if (E.initialized) {
                    E.initialized = false;
                    return false
                }
            });
            if (C.browser.msie) {
                this.unselectable = this.element.attr("unselectable");
                this.element.attr("unselectable", "on")
            }
        }, destroy: function() {
            this.element.unbind(".mouse").removeData("mouse");
            (C.browser.msie && this.element.attr("unselectable", this.unselectable))
        }, trigger: function() {
            return this.click.apply(this, arguments)
        }, click: function(G) {
            if (G.which != 1 || C.inArray(G.target.nodeName.toLowerCase(), this.options.dragPrevention || []) != -1 || (this.options.condition && !this.options.condition.apply(this.options.executor || this, [G, this.element]))) {
                return true
            }
            var F = this;
            this.initialized = false;
            var E = function() {
                F._MP = {left: G.pageX, top: G.pageY};
                C(document).bind("mouseup.mouse", function() {
                    return F.stop.apply(F, arguments)
                });
                C(document).bind("mousemove.mouse", function() {
                    return F.drag.apply(F, arguments)
                });
                if (!F.initalized && Math.abs(F._MP.left - G.pageX) >= F.options.distance || Math.abs(F._MP.top - G.pageY) >= F.options.distance) {
                    (F.options.start && F.options.start.call(F.options.executor || F, G, F.element));
                    (F.options.drag && F.options.drag.call(F.options.executor || F, G, this.element));
                    F.initialized = true
                }
            };
            if (this.options.delay) {
                if (this.timer) {
                    clearInterval(this.timer)
                }
                this.timer = setTimeout(E, this.options.delay)
            } else {
                E()
            }
            return false
        }, stop: function(E) {
            if (!this.initialized) {
                return C(document).unbind("mouseup.mouse").unbind("mousemove.mouse")
            }
            (this.options.stop && this.options.stop.call(this.options.executor || this, E, this.element));
            C(document).unbind("mouseup.mouse").unbind("mousemove.mouse");
            return false
        }, drag: function(E) {
            var F = this.options;
            if (C.browser.msie && !E.button) {
                return this.stop.call(this, E)
            }
            if (!this.initialized && (Math.abs(this._MP.left - E.pageX) >= F.distance || Math.abs(this._MP.top - E.pageY) >= F.distance)) {
                (F.start && F.start.call(F.executor || this, E, this.element));
                this.initialized = true
            } else {
                if (!this.initialized) {
                    return false
                }
            }
            (F.drag && F.drag.call(this.options.executor || this, E, this.element));
            return false
        }})
})(jQuery);
(function(A) {
    A.widget("ui.tabs", {init: function() {
            var B = this;
            this.options.event += ".tabs";
            A(this.element).bind("setData.tabs", function(D, C, E) {
                if ((/^selected/).test(C)) {
                    B.select(E)
                } else {
                    B.options[C] = E;
                    B.tabify()
                }
            }).bind("getData.tabs", function(D, C) {
                return B.options[C]
            });
            this.tabify(true)
        }, length: function() {
            return this.$tabs.length
        }, tabId: function(B) {
            return B.title && B.title.replace(/\s/g, "_").replace(/[^A-Za-z0-9\-_:\.]/g, "") || this.options.idPrefix + A.data(B)
        }, ui: function(C, B) {
            return{instance: this, options: this.options, tab: C, panel: B}
        }, tabify: function(N) {
            this.$lis = A("li:has(a[href])", this.element);
            this.$tabs = this.$lis.map(function() {
                return A("a", this)[0]
            });
            this.$panels = A([]);
            var O = this, D = this.options;
            this.$tabs.each(function(Q, P) {
                if (P.hash && P.hash.replace("#", "")) {
                    O.$panels = O.$panels.add(P.hash)
                } else {
                    if (A(P).attr("href") != "#") {
                        A.data(P, "href.tabs", P.href);
                        A.data(P, "load.tabs", P.href);
                        var S = O.tabId(P);
                        P.href = "#" + S;
                        var R = A("#" + S);
                        if (!R.length) {
                            R = A(D.panelTemplate).attr("id", S).addClass(D.panelClass).insertAfter(O.$panels[Q - 1] || O.element);
                            R.data("destroy.tabs", true)
                        }
                        O.$panels = O.$panels.add(R)
                    } else {
                        D.disabled.push(Q + 1)
                    }
                }
            });
            if (N) {
                A(this.element).hasClass(D.navClass) || A(this.element).addClass(D.navClass);
                this.$panels.each(function() {
                    var P = A(this);
                    P.hasClass(D.panelClass) || P.addClass(D.panelClass)
                });
                this.$tabs.each(function(S, P) {
                    if (location.hash) {
                        if (P.hash == location.hash) {
                            D.selected = S;
                            if (A.browser.msie || A.browser.opera) {
                                var R = A(location.hash), T = R.attr("id");
                                R.attr("id", "");
                                setTimeout(function() {
                                    R.attr("id", T)
                                }, 500)
                            }
                            scrollTo(0, 0);
                            return false
                        }
                    } else {
                        if (D.cookie) {
                            var Q = parseInt(A.cookie("ui-tabs" + A.data(O.element)), 10);
                            if (Q && O.$tabs[Q]) {
                                D.selected = Q;
                                return false
                            }
                        } else {
                            if (O.$lis.eq(S).hasClass(D.selectedClass)) {
                                D.selected = S;
                                return false
                            }
                        }
                    }
                });
                this.$panels.addClass(D.hideClass);
                this.$lis.removeClass(D.selectedClass);
                if (D.selected !== null) {
                    this.$panels.eq(D.selected).show().removeClass(D.hideClass);
                    this.$lis.eq(D.selected).addClass(D.selectedClass);
                    var J = function() {
                        A(O.element).triggerHandler("tabsshow", [O.ui(O.$tabs[D.selected], O.$panels[D.selected])], D.show)
                    };
                    if (A.data(this.$tabs[D.selected], "load.tabs")) {
                        this.load(D.selected, J)
                    } else {
                        J()
                    }
                }
                D.disabled = A.unique(D.disabled.concat(A.map(this.$lis.filter("." + D.disabledClass), function(Q, P) {
                    return O.$lis.index(Q)
                }))).sort();
                A(window).bind("unload", function() {
                    O.$tabs.unbind(".tabs");
                    O.$lis = O.$tabs = O.$panels = null
                })
            }
            for (var G = 0, M; M = this.$lis[G]; G++) {
                A(M)[A.inArray(G, D.disabled) != -1 && !A(M).hasClass(D.selectedClass) ? "addClass" : "removeClass"](D.disabledClass)
            }
            if (D.cache === false) {
                this.$tabs.removeData("cache.tabs")
            }
            var C, I, B = {"min-width": 0, duration: 1}, E = "normal";
            if (D.fx && D.fx.constructor == Array) {
                C = D.fx[0] || B, I = D.fx[1] || B
            } else {
                C = I = D.fx || B
            }
            var H = {display: "", overflow: "", height: ""};
            if (!A.browser.msie) {
                H.opacity = ""
            }
            function L(Q, P, R) {
                P.animate(C, C.duration || E, function() {
                    P.addClass(D.hideClass).css(H);
                    if (A.browser.msie && C.opacity) {
                        P[0].style.filter = ""
                    }
                    if (R) {
                        K(Q, R, P)
                    }
                })
            }
            function K(Q, R, P) {
                if (I === B) {
                    R.css("display", "block")
                }
                R.animate(I, I.duration || E, function() {
                    R.removeClass(D.hideClass).css(H);
                    if (A.browser.msie && I.opacity) {
                        R[0].style.filter = ""
                    }
                    A(O.element).triggerHandler("tabsshow", [O.ui(Q, R[0])], D.show)
                })
            }
            function F(Q, S, P, R) {
                S.addClass(D.selectedClass).siblings().removeClass(D.selectedClass);
                L(Q, P, R)
            }
            this.$tabs.unbind(".tabs").bind(D.event, function() {
                var S = A(this).parents("li:eq(0)"), P = O.$panels.filter(":visible"), R = A(this.hash);
                if ((S.hasClass(D.selectedClass) && !D.unselect) || S.hasClass(D.disabledClass) || A(this).hasClass(D.loadingClass) || A(O.element).triggerHandler("tabsselect", [O.ui(this, R[0])], D.select) === false) {
                    this.blur();
                    return false
                }
                O.options.selected = O.$tabs.index(this);
                if (D.unselect) {
                    if (S.hasClass(D.selectedClass)) {
                        O.options.selected = null;
                        S.removeClass(D.selectedClass);
                        O.$panels.stop();
                        L(this, P);
                        this.blur();
                        return false
                    } else {
                        if (!P.length) {
                            O.$panels.stop();
                            var Q = this;
                            O.load(O.$tabs.index(this), function() {
                                S.addClass(D.selectedClass).addClass(D.unselectClass);
                                K(Q, R)
                            });
                            this.blur();
                            return false
                        }
                    }
                }
                if (D.cookie) {
                    A.cookie("ui-tabs" + A.data(O.element), O.options.selected, D.cookie)
                }
                O.$panels.stop();
                if (R.length) {
                    var Q = this;
                    O.load(O.$tabs.index(this), P.length ? function() {
                        F(Q, S, P, R)
                    } : function() {
                        S.addClass(D.selectedClass);
                        K(Q, R)
                    })
                } else {
                    throw"jQuery UI Tabs: Mismatching fragment identifier."
                }
                if (A.browser.msie) {
                    this.blur()
                }
                return false
            });
            if (!(/^click/).test(D.event)) {
                this.$tabs.bind("click.tabs", function() {
                    return false
                })
            }
        }, add: function(E, D, C) {
            if (C == undefined) {
                C = this.$tabs.length
            }
            var G = this.options;
            var I = A(G.tabTemplate.replace(/#\{href\}/, E).replace(/#\{label\}/, D));
            I.data("destroy.tabs", true);
            var H = E.indexOf("#") == 0 ? E.replace("#", "") : this.tabId(A("a:first-child", I)[0]);
            var F = A("#" + H);
            if (!F.length) {
                F = A(G.panelTemplate).attr("id", H).addClass(G.panelClass).addClass(G.hideClass);
                F.data("destroy.tabs", true)
            }
            if (C >= this.$lis.length) {
                I.appendTo(this.element);
                F.appendTo(this.element[0].parentNode)
            } else {
                I.insertBefore(this.$lis[C]);
                F.insertBefore(this.$panels[C])
            }
            G.disabled = A.map(G.disabled, function(K, J) {
                return K >= C ? ++K : K
            });
            this.tabify();
            if (this.$tabs.length == 1) {
                I.addClass(G.selectedClass);
                F.removeClass(G.hideClass);
                var B = A.data(this.$tabs[0], "load.tabs");
                if (B) {
                    this.load(C, B)
                }
            }
            A(this.element).triggerHandler("tabsadd", [this.ui(this.$tabs[C], this.$panels[C])], G.add)
        }, remove: function(B) {
            var D = this.options, E = this.$lis.eq(B).remove(), C = this.$panels.eq(B).remove();
            if (E.hasClass(D.selectedClass) && this.$tabs.length > 1) {
                this.select(B + (B + 1 < this.$tabs.length ? 1 : -1))
            }
            D.disabled = A.map(A.grep(D.disabled, function(G, F) {
                return G != B
            }), function(G, F) {
                return G >= B ? --G : G
            });
            this.tabify();
            A(this.element).triggerHandler("tabsremove", [this.ui(E.find("a")[0], C[0])], D.remove)
        }, enable: function(B) {
            var C = this.options;
            if (A.inArray(B, C.disabled) == -1) {
                return
            }
            var D = this.$lis.eq(B).removeClass(C.disabledClass);
            if (A.browser.safari) {
                D.css("display", "inline-block");
                setTimeout(function() {
                    D.css("display", "block")
                }, 0)
            }
            C.disabled = A.grep(C.disabled, function(F, E) {
                return F != B
            });
            A(this.element).triggerHandler("tabsenable", [this.ui(this.$tabs[B], this.$panels[B])], C.enable)
        }, disable: function(C) {
            var B = this, D = this.options;
            if (C != D.selected) {
                this.$lis.eq(C).addClass(D.disabledClass);
                D.disabled.push(C);
                D.disabled.sort();
                A(this.element).triggerHandler("tabsdisable", [this.ui(this.$tabs[C], this.$panels[C])], D.disable)
            }
        }, select: function(B) {
            if (typeof B == "string") {
                B = this.$tabs.index(this.$tabs.filter("[href$=" + B + "]")[0])
            }
            this.$tabs.eq(B).trigger(this.options.event)
        }, load: function(F, K) {
            var L = this, C = this.options, D = this.$tabs.eq(F), J = D[0], G = K == undefined || K === false, B = D.data("load.tabs");
            K = K || function() {
            };
            if (!B || (A.data(J, "cache.tabs") && !G)) {
                K();
                return
            }
            if (C.spinner) {
                var H = A("span", J);
                H.data("label.tabs", H.html()).html("<em>" + C.spinner + "</em>")
            }
            var I = function() {
                L.$tabs.filter("." + C.loadingClass).each(function() {
                    A(this).removeClass(C.loadingClass);
                    if (C.spinner) {
                        var M = A("span", this);
                        M.html(M.data("label.tabs")).removeData("label.tabs")
                    }
                });
                L.xhr = null
            };
            var E = A.extend({}, C.ajaxOptions, {url: B, success: function(N, M) {
                    A(J.hash).html(N);
                    I();
                    if (C.cache) {
                        A.data(J, "cache.tabs", true)
                    }
                    A(L.element).triggerHandler("tabsload", [L.ui(L.$tabs[F], L.$panels[F])], C.load);
                    C.ajaxOptions.success && C.ajaxOptions.success(N, M);
                    K()
                }});
            if (this.xhr) {
                this.xhr.abort();
                I()
            }
            D.addClass(C.loadingClass);
            setTimeout(function() {
                L.xhr = A.ajax(E)
            }, 0)
        }, url: function(C, B) {
            this.$tabs.eq(C).removeData("cache.tabs").data("load.tabs", B)
        }, destroy: function() {
            var B = this.options;
            A(this.element).unbind(".tabs").removeClass(B.navClass).removeData("tabs");
            this.$tabs.each(function() {
                var C = A.data(this, "href.tabs");
                if (C) {
                    this.href = C
                }
                var D = A(this).unbind(".tabs");
                A.each(["href", "load", "cache"], function(E, F) {
                    D.removeData(F + ".tabs")
                })
            });
            this.$lis.add(this.$panels).each(function() {
                if (A.data(this, "destroy.tabs")) {
                    A(this).remove()
                } else {
                    A(this).removeClass([B.selectedClass, B.unselectClass, B.disabledClass, B.panelClass, B.hideClass].join(" "))
                }
            })
        }});
    A.ui.tabs.defaults = {selected: 0, unselect: false, event: "click", disabled: [], cookie: null, spinner: "Loading&#8230;", cache: false, idPrefix: "ui-tabs-", ajaxOptions: {}, fx: null, tabTemplate: '<li><a href="#{href}"><span>#{label}</span></a></li>', panelTemplate: "<div></div>", navClass: "ui-tabs-nav", selectedClass: "ui-tabs-selected", unselectClass: "ui-tabs-unselect", disabledClass: "ui-tabs-disabled", panelClass: "ui-tabs-panel", hideClass: "ui-tabs-hide", loadingClass: "ui-tabs-loading"};
    A.ui.tabs.getter = "length";
    A.extend(A.ui.tabs.prototype, {rotation: null, rotate: function(C, F) {
            F = F || false;
            var B = this, E = this.options.selected;
            function G() {
                B.rotation = setInterval(function() {
                    E = ++E < B.$tabs.length ? E : 0;
                    B.select(E)
                }, C)
            }
            function D(H) {
                if (!H || H.clientX) {
                    clearInterval(B.rotation)
                }
            }
            if (C) {
                G();
                if (!F) {
                    this.$tabs.bind(this.options.event, D)
                } else {
                    this.$tabs.bind(this.options.event, function() {
                        D();
                        E = B.options.selected;
                        G()
                    })
                }
            } else {
                D();
                this.$tabs.unbind(this.options.event, D)
            }
        }})
})(jQuery);