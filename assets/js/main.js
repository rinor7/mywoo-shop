/**
 * Base Theme — storefront
 *
 * Cart calls go straight to WooCommerce's own wc-ajax endpoints
 * (add_to_cart / remove_from_cart), which return the fragments registered in
 * includes/shop/cart.php. Nothing here depends on WooCommerce's frontend JS.
 */
(function () {
    'use strict';

    var MS = window.MyShop || { i18n: {} };
    var i18n = MS.i18n || {};
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

    /* ==========================================================
       Toasts
    ========================================================== */
    var toastStack = qs('.js-toasts');

    function toast(message, icon) {
        if (!toastStack || !message) { return; }

        var el = document.createElement('div');
        el.className = 'toast';

        var i = document.createElement('i');
        i.className = 'fa-solid ' + (icon || 'fa-circle-check');

        var span = document.createElement('span');
        span.textContent = message;

        el.appendChild(i);
        el.appendChild(span);
        toastStack.appendChild(el);

        requestAnimationFrame(function () { el.classList.add('is-visible'); });

        setTimeout(function () {
            el.classList.remove('is-visible');
            setTimeout(function () { el.remove(); }, 400);
        }, 3200);
    }

    /* ==========================================================
       Overlays (cart drawer, menu drawer, search, quick view)
    ========================================================== */
    var backdrop = qs('.js-backdrop');
    var openEl = null;
    var lastFocus = null;

    function openOverlay(el) {
        if (!el) { return; }

        closeOverlay();
        lastFocus = document.activeElement;
        openEl = el;

        el.hidden = false;
        if (backdrop) { backdrop.hidden = false; }
        document.body.classList.add('is-locked');

        requestAnimationFrame(function () {
            el.classList.add('is-open');
            if (backdrop) { backdrop.classList.add('is-open'); }

            var focusTarget = el.querySelector('.js-search-input') || el.querySelector('button, a, input');
            if (focusTarget) { focusTarget.focus(); }
        });
    }

    function closeOverlay() {
        if (!openEl) { return; }

        var el = openEl;
        openEl = null;

        el.classList.remove('is-open');
        if (backdrop) { backdrop.classList.remove('is-open'); }
        document.body.classList.remove('is-locked');

        if (lastFocus && lastFocus.focus) { lastFocus.focus(); }

        setTimeout(function () {
            el.hidden = true;
            // Another overlay may have opened in the meantime — leave its backdrop up.
            if (backdrop && !openEl) { backdrop.hidden = true; }
        }, 450);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.js-cart-open')) {
            e.preventDefault();
            openOverlay(qs('.js-cart-drawer'));
        } else if (e.target.closest('.js-menu-open')) {
            e.preventDefault();
            openOverlay(qs('.js-menu-drawer'));
        } else if (e.target.closest('.js-search-open')) {
            e.preventDefault();
            openOverlay(qs('.js-search-overlay'));
        } else if (e.target.closest('.js-drawer-close') || e.target === backdrop) {
            closeOverlay();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeOverlay(); }
    });

    /* ==========================================================
       Announcement bar
    ========================================================== */
    (function () {
        var bar = qs('.js-announce');
        if (!bar) { return; }

        if (sessionStorage.getItem('ms-announce-off') === '1') {
            bar.classList.add('is-dismissed');
            return;
        }

        var items = qsa('.announce__item', bar);
        var index = 0;
        var timer = null;

        function show(next) {
            if (items.length < 2) { return; }
            items[index].classList.remove('is-active');
            index = (next + items.length) % items.length;
            items[index].classList.add('is-active');
        }

        function auto() {
            if (reduceMotion || items.length < 2) { return; }
            clearInterval(timer);
            timer = setInterval(function () { show(index + 1); }, 5000);
        }

        var next = qs('.js-announce-next', bar);
        var prev = qs('.js-announce-prev', bar);
        var close = qs('.js-announce-close', bar);

        if (next) { next.addEventListener('click', function () { show(index + 1); auto(); }); }
        if (prev) { prev.addEventListener('click', function () { show(index - 1); auto(); }); }
        if (close) {
            close.addEventListener('click', function () {
                bar.classList.add('is-dismissed');
                sessionStorage.setItem('ms-announce-off', '1');
                clearInterval(timer);
            });
        }

        auto();
    }());

    /* ==========================================================
       Header — sticky + hide on scroll down
    ========================================================== */
    (function () {
        var header = qs('.js-header');
        if (!header) { return; }

        var lastY = window.scrollY;
        var ticking = false;

        function setHeight() {
            document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
        }

        function onScroll() {
            var y = window.scrollY;

            header.classList.toggle('is-stuck', y > 10);

            // Only start hiding once well past the fold, and never while an overlay is open.
            if (!openEl && y > 400) {
                header.classList.toggle('is-hidden', y > lastY);
            } else {
                header.classList.remove('is-hidden');
            }

            lastY = y;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(onScroll);
            }
        }, { passive: true });

        window.addEventListener('resize', setHeight);
        setHeight();
        onScroll();
    }());

    /* ==========================================================
       Sliders

       Initialized at window.load, not at parse time: measuring while the
       viewport/webfonts are still settling bakes a wrong slide width in and
       shows a sliver of the neighbouring slide. Until init, slides are
       plain stacked blocks (CSS gates the entrance animation on
       .swiper-initialized), so nothing flashes.
    ========================================================== */
    var swipers = [];

    function initSliders() {
        if (qs('.js-hero')) {
            swipers.push(new Swiper('.js-hero', {
                speed: 900,
                loop: true,
                observer: true,
                observeParents: true,
                autoplay: reduceMotion ? false : { delay: 6500, disableOnInteraction: false },
                pagination: { el: '.js-hero-pagination', clickable: true },
                navigation: { nextEl: '.js-hero-next', prevEl: '.js-hero-prev' }
            }));
        }

        if (qs('.js-product-slider')) {
            var progressBar = qs('.js-product-progress');

            var updateProgress = function (sw) {
                if (!progressBar) { return; }

                var perView = sw.params.slidesPerView;
                var total = sw.slides.length;
                var ratio = Math.min(1, perView / total);

                progressBar.style.width = (ratio * 100) + '%';
                progressBar.style.transform =
                    'translateX(' + (((1 - ratio) / ratio) * 100 * sw.progress) + '%)';
            };

            swipers.push(new Swiper('.js-product-slider', {
                slidesPerView: 1.25,
                spaceBetween: 14,
                navigation: { nextEl: '.js-product-next', prevEl: '.js-product-prev' },
                breakpoints: {
                    480: { slidesPerView: 2, spaceBetween: 16 },
                    768: { slidesPerView: 3, spaceBetween: 20 },
                    1200: { slidesPerView: 4, spaceBetween: 24 }
                },
                on: {
                    init: updateProgress,
                    resize: updateProgress,
                    progress: updateProgress
                }
            }));
        }

        if (qs('.js-reviews')) {
            swipers.push(new Swiper('.js-reviews', {
                slidesPerView: 1,
                spaceBetween: 16,
                pagination: { el: '.js-reviews-pagination', clickable: true },
                breakpoints: {
                    768: { slidesPerView: 2, spaceBetween: 20 },
                    1200: { slidesPerView: 3, spaceBetween: 24 }
                }
            }));
        }

        // Product gallery: main slider + clickable thumb strip, kept in sync.
        if (qs('.js-pdp-main')) {
            var thumbsEl = qs('.js-pdp-thumbs');
            var thumbsSwiper = null;

            if (thumbsEl) {
                thumbsSwiper = new Swiper(thumbsEl, {
                    slidesPerView: 'auto',
                    spaceBetween: 8,
                    watchSlidesProgress: true
                });
                swipers.push(thumbsSwiper);
            }

            swipers.push(new Swiper('.js-pdp-main', {
                speed: 650,
                navigation: { nextEl: '.js-pdp-next', prevEl: '.js-pdp-prev' },
                thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined
            }));
        }

        // Safety net for width changes after init (orientation, late fonts):
        // fully re-measure and snap back to the active slide.
        var resync = function (sw) {
            sw.updateSize();
            sw.updateSlides();
            sw.updateProgress();
            sw.updateSlidesClasses();
            if (sw.params.loop) {
                sw.slideToLoop(sw.realIndex, 0, false);
            } else {
                sw.slideTo(sw.activeIndex, 0, false);
            }
        };

        if (window.ResizeObserver) {
            var ro = new ResizeObserver(function () {
                swipers.forEach(resync);
            });
            swipers.forEach(function (sw) { ro.observe(sw.el); });
        }
    }

    if (window.Swiper) {
        if (document.readyState === 'complete') {
            initSliders();
        } else {
            window.addEventListener('load', initSliders);
        }
    }

    /* ==========================================================
       Product tabs
    ========================================================== */
    (function () {
        var buttons = qsa('.js-tab');
        if (!buttons.length) { return; }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.dataset.tab;

                buttons.forEach(function (b) {
                    var on = b === btn;
                    b.classList.toggle('is-active', on);
                    b.setAttribute('aria-selected', on ? 'true' : 'false');
                });

                qsa('.js-tab-panel').forEach(function (panel) {
                    var on = panel.dataset.panel === target;
                    panel.classList.toggle('is-active', on);
                    panel.hidden = !on;
                });
            });
        });
    }());

    /* ==========================================================
       Countdown
    ========================================================== */
    (function () {
        var box = qs('.js-countdown');
        if (!box) { return; }

        var ends = parseInt(box.dataset.ends, 10) * 1000;
        var out = {
            days: qs('.js-cd-days', box),
            hours: qs('.js-cd-hours', box),
            mins: qs('.js-cd-mins', box),
            secs: qs('.js-cd-secs', box)
        };

        function pad(n) { return n < 10 ? '0' + n : String(n); }

        function tick() {
            var left = ends - Date.now();
            if (left < 0) { left = 0; }

            var s = Math.floor(left / 1000);
            out.days.textContent = pad(Math.floor(s / 86400));
            out.hours.textContent = pad(Math.floor((s % 86400) / 3600));
            out.mins.textContent = pad(Math.floor((s % 3600) / 60));
            out.secs.textContent = pad(s % 60);
        }

        tick();
        setInterval(tick, 1000);
    }());

    /* ==========================================================
       Wishlist (local only — no server state)
    ========================================================== */
    (function () {
        var KEY = 'ms-wishlist';

        function read() {
            try {
                return JSON.parse(localStorage.getItem(KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function write(list) {
            localStorage.setItem(KEY, JSON.stringify(list));
            var count = qs('.js-wish-count');
            if (count) {
                count.textContent = list.length;
                count.classList.toggle('is-empty', !list.length);
            }
        }

        function paint() {
            var list = read();
            qsa('.js-wishlist').forEach(function (btn) {
                var on = list.indexOf(btn.dataset.id) > -1;
                btn.classList.toggle('is-active', on);
                var icon = btn.querySelector('i');
                if (icon) { icon.className = (on ? 'fa-solid' : 'fa-regular') + ' fa-heart'; }
            });
            write(list);
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.js-wishlist');
            if (!btn) { return; }

            e.preventDefault();

            // YITH bridge: adds server-side; a second tap opens the wishlist.
            if (MS.yith && MS.yithAdd) {
                if (btn.classList.contains('is-active')) {
                    window.location.href = MS.wishlistUrl;
                    return;
                }

                fetch(MS.yithAdd.replace('__ID__', btn.dataset.id), { credentials: 'same-origin' })
                    .then(function (r) {
                        if (!r.ok) { throw new Error('wishlist'); }
                        return r.json().catch(function () { return {}; });
                    })
                    .then(function () {
                        btn.classList.add('is-active');
                        var icon = btn.querySelector('i');
                        if (icon) { icon.className = 'fa-solid fa-heart'; }

                        qsa('.js-wish-count').forEach(function (el) {
                            el.textContent = (parseInt(el.textContent, 10) || 0) + 1;
                            el.classList.remove('is-empty');
                            el.classList.add('is-bumped');
                        });

                        toast(i18n.saved, 'fa-heart');
                    })
                    .catch(function () {
                        toast(i18n.error, 'fa-triangle-exclamation');
                    });
                return;
            }

            var list = read();
            var id = btn.dataset.id;
            var at = list.indexOf(id);

            if (at > -1) {
                list.splice(at, 1);
                toast(i18n.unsaved, 'fa-heart-crack');
            } else {
                list.push(id);
                toast(i18n.saved, 'fa-heart');
            }

            write(list);
            paint();
        });

        // With YITH active the hearts render their state server-side.
        if (!MS.yith) {
            paint();
        }
    }());

    /* ==========================================================
       Cart — WooCommerce wc-ajax
    ========================================================== */
    function endpoint(name) {
        return MS.wcAjax ? MS.wcAjax.replace('%%endpoint%%', name) : '';
    }

    function bumpCartCount() {
        qsa('.js-cart-count').forEach(function (el) {
            el.classList.add('is-bumped');
            setTimeout(function () { el.classList.remove('is-bumped'); }, 500);
        });
    }

    function applyFragments(fragments) {
        if (!fragments) { return; }

        Object.keys(fragments).forEach(function (selector) {
            var nodes = qsa(selector);
            if (!nodes.length) { return; }

            nodes.forEach(function (node) {
                var tmp = document.createElement('div');
                tmp.innerHTML = fragments[selector];
                var fresh = tmp.firstElementChild;
                if (fresh) { node.replaceWith(fresh); }
            });
        });

        bumpCartCount();
    }

    function post(name, data, onDone, onFail) {
        var url = endpoint(name);
        if (!url) {
            if (onFail) { onFail(); }
            return;
        }

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams(data)
        })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (!json || json.error) {
                    if (onFail) { onFail(); }
                    return;
                }
                onDone(json);
            })
            .catch(function () {
                if (onFail) { onFail(); }
            });
    }

    // Add to cart (product cards, deal section, quick view)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-add-to-cart');
        if (!btn) { return; }

        e.preventDefault();

        // Real product that needs options (variable/grouped) — go pick them.
        if (btn.dataset.goto) {
            window.location.href = btn.dataset.goto;
            return;
        }

        if (btn.dataset.demo) {
            toast(i18n.demo, 'fa-circle-info');
            return;
        }

        var qtyInput = qs('.js-qty-input');
        var quantity = (btn.classList.contains('js-qv-add') && qtyInput) ? qtyInput.value : 1;

        btn.classList.add('is-loading');

        post(
            'add_to_cart',
            { product_id: btn.dataset.productId, quantity: quantity },
            function (json) {
                btn.classList.remove('is-loading');
                btn.classList.add('is-done');
                setTimeout(function () { btn.classList.remove('is-done'); }, 1400);

                applyFragments(json.fragments);
                toast(i18n.added);
                openOverlay(qs('.js-cart-drawer'));
            },
            function () {
                btn.classList.remove('is-loading');
                toast(i18n.error, 'fa-triangle-exclamation');
            }
        );
    });

    // Drawer quantity stepper — delegated, so it survives fragment replacement
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-drawer-qty');
        if (!btn) { return; }

        var box = btn.closest('.drawer-qty');
        var val = box.querySelector('.drawer-qty__val');
        var qty = (parseInt(val.dataset.qty, 10) || 1) + parseInt(btn.dataset.dir, 10);

        if (qty < 0) { return; }

        box.classList.add('is-busy');

        post(
            'myshop_set_qty',
            { cart_item_key: box.dataset.key, quantity: qty },
            function (json) {
                applyFragments(json.fragments);
                if (qty === 0) { toast(i18n.removed, 'fa-trash-can'); }
            },
            function () {
                box.classList.remove('is-busy');
                toast(i18n.error, 'fa-triangle-exclamation');
            }
        );
    });

    // Remove from cart — delegated, so it survives fragment replacement
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-cart-remove');
        if (!btn) { return; }

        e.preventDefault();
        btn.disabled = true;

        post(
            'remove_from_cart',
            { cart_item_key: btn.dataset.key },
            function (json) {
                applyFragments(json.fragments);
                toast(i18n.removed, 'fa-trash-can');
            },
            function () {
                btn.disabled = false;
                toast(i18n.error, 'fa-triangle-exclamation');
            }
        );
    });

    /* ==========================================================
       Checkout coupon (summary card)
       Not a nested <form> — we call Woo's apply_coupon endpoint and
       let the core checkout JS refresh the totals.
    ========================================================== */
    (function () {
        var box = qs('.js-checkout-coupon');
        if (!box) { return; }

        var input = qs('#coupon_code', box);
        var button = qs('.js-coupon-apply', box);
        var msg = qs('.js-coupon-msg', box);

        function apply() {
            var code = input ? input.value.trim() : '';
            if (!code) {
                if (input) { input.focus(); }
                return;
            }

            button.disabled = true;
            box.classList.add('is-busy');

            fetch(endpoint('apply_coupon'), {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new URLSearchParams({ coupon_code: code, security: MS.couponNonce || '' })
            })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    button.disabled = false;
                    box.classList.remove('is-busy');

                    if (msg) { msg.innerHTML = html; }
                    if (html.indexOf('woocommerce-error') === -1 && input) { input.value = ''; }

                    // Woo's checkout JS re-renders the order review.
                    if (window.jQuery) { window.jQuery(document.body).trigger('update_checkout'); }
                })
                .catch(function () {
                    button.disabled = false;
                    box.classList.remove('is-busy');
                    toast(i18n.error, 'fa-triangle-exclamation');
                });
        }

        button.addEventListener('click', apply);

        // Enter inside the coupon field must not submit the checkout form.
        if (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    apply();
                }
            });
        }
    }());

    /* ==========================================================
       Quick view
    ========================================================== */
    (function () {
        var modal = qs('.js-quickview-modal');
        if (!modal) { return; }

        function starsHtml(rating) {
            var html = '';
            for (var i = 1; i <= 5; i++) {
                if (rating >= i) {
                    html += '<i class="fa-solid fa-star"></i>';
                } else if (rating > i - 1) {
                    html += '<i class="fa-solid fa-star-half-stroke"></i>';
                } else {
                    html += '<i class="fa-solid fa-star stars__empty"></i>';
                }
            }
            return '<span class="stars">' + html + '</span>';
        }

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.js-quickview');
            if (!trigger) { return; }

            e.preventDefault();

            var card = trigger.closest('[data-product]');
            if (!card) { return; }

            var data;
            try {
                data = JSON.parse(card.dataset.product);
            } catch (err) {
                return;
            }

            qs('.js-qv-image', modal).src = data.image;
            qs('.js-qv-image', modal).alt = data.name;
            qs('.js-qv-cat', modal).textContent = data.category || '';
            qs('.js-qv-title', modal).textContent = data.name;
            qs('.js-qv-price', modal).innerHTML = data.price;
            qs('.js-qv-excerpt', modal).textContent = data.excerpt || '';
            qs('.js-qv-stars', modal).innerHTML = data.rating ? starsHtml(data.rating) : '';
            qs('.js-qv-count', modal).textContent = data.count ? '(' + data.count + ')' : '';
            qs('.js-qv-link', modal).href = data.url;

            var addBtn = qs('.js-qv-add', modal);
            var addLabel = addBtn.querySelector('span');
            var qtyBox = qs('.qty', modal);

            addBtn.dataset.productId = data.id;
            delete addBtn.dataset.demo;
            delete addBtn.dataset.goto;

            if (data.demo) {
                addBtn.dataset.demo = '1';
                if (addLabel) { addLabel.textContent = i18n.addToBag; }
                if (qtyBox) { qtyBox.hidden = false; }
            } else if (!data.buy) {
                // Needs options — the button becomes the way to the product page.
                addBtn.dataset.goto = data.url;
                if (addLabel) { addLabel.textContent = i18n.chooseOptions; }
                if (qtyBox) { qtyBox.hidden = true; }
            } else {
                if (addLabel) { addLabel.textContent = i18n.addToBag; }
                if (qtyBox) { qtyBox.hidden = false; }
            }

            var qty = qs('.js-qty-input', modal);
            if (qty) { qty.value = 1; }

            openOverlay(modal);
        });

        // Quantity stepper
        modal.addEventListener('click', function (e) {
            var input = qs('.js-qty-input', modal);
            if (!input) { return; }

            var value = parseInt(input.value, 10) || 1;

            if (e.target.closest('.js-qty-plus')) {
                input.value = value + 1;
            } else if (e.target.closest('.js-qty-minus')) {
                input.value = Math.max(1, value - 1);
            }
        });
    }());

    /* ==========================================================
       Product page: qty stepper + sticky add-to-bag bar
       The bar proxies the real add-to-cart form, so quantity and
       validation stay single-source.
    ========================================================== */
    (function () {
        var form = qs('.pdp-panel__form form.cart');
        if (!form) { return; }

        // Dress Woo's quantity input as a stepper.
        qsa('.quantity', form).forEach(function (quantity) {
            if (quantity.closest('.qty-stepper')) { return; }

            var wrap = document.createElement('div');
            wrap.className = 'qty-stepper';

            var minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'qty-stepper__btn js-cart-qty';
            minus.dataset.dir = '-1';
            minus.setAttribute('aria-label', 'Decrease quantity');
            minus.innerHTML = '&minus;';

            var plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'qty-stepper__btn js-cart-qty';
            plus.dataset.dir = '1';
            plus.setAttribute('aria-label', 'Increase quantity');
            plus.textContent = '+';

            quantity.parentNode.insertBefore(wrap, quantity);
            wrap.appendChild(minus);
            wrap.appendChild(quantity);
            wrap.appendChild(plus);
        });

        var bar = qs('.js-pdp-bar');
        if (!bar) { return; }

        var panel = qs('.js-pdp-panel');
        var qty = qs('input.qty', form);
        var count = qs('.js-pdp-bar-count', bar);
        var addBtn = qs('.single_add_to_cart_button', form);

        bar.removeAttribute('hidden');

        function syncCount() {
            if (count && qty) { count.textContent = qty.value || '1'; }
        }

        if (qty) {
            qty.addEventListener('change', syncCount);
            qty.addEventListener('input', syncCount);
        }
        syncCount();

        // Bar steppers drive the real input.
        bar.addEventListener('click', function (e) {
            var stepBtn = e.target.closest('[data-dir]');
            if (stepBtn && qty) {
                var min = qty.min === '' ? 1 : parseFloat(qty.min) || 1;
                var max = qty.max === '' ? Infinity : parseFloat(qty.max);
                var next = (parseFloat(qty.value) || 1) + parseInt(stepBtn.dataset.dir, 10);
                qty.value = Math.min(max, Math.max(min, next));
                qty.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }

            if (e.target.closest('.js-pdp-bar-add') && addBtn) {
                if (addBtn.disabled || addBtn.classList.contains('disabled')) {
                    // Variable product without a chosen variation — take them to the form.
                    panel.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
                    return;
                }
                addBtn.click();
            }
        });

        // Show the bar once the purchase panel scrolls out of view (upwards).
        if ('IntersectionObserver' in window && panel) {
            var barIO = new IntersectionObserver(function (entries) {
                var entry = entries[0];
                var passed = !entry.isIntersecting && entry.boundingClientRect.top < 0;
                bar.classList.toggle('is-visible', passed);
            }, { threshold: 0 });

            barIO.observe(panel);
        }
    }());

    /* ==========================================================
       Cart page quantity steppers
       Fires a bubbling `change` so WooCommerce's cart JS enables
       the Update-cart button.
    ========================================================== */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-cart-qty');
        if (!btn) { return; }

        var input = btn.closest('.qty-stepper').querySelector('input.qty');
        if (!input) { return; }

        var step = parseFloat(input.step) || 1;
        var min = input.min === '' ? 0 : parseFloat(input.min);
        var max = input.max === '' ? Infinity : parseFloat(input.max);
        var value = (parseFloat(input.value) || 0) + step * parseInt(btn.dataset.dir, 10);

        input.value = Math.min(max, Math.max(min, value));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    /* ==========================================================
       Newsletter (front-end only for now)
    ========================================================== */
    (function () {
        var form = qs('.js-newsletter');
        if (!form) { return; }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var input = form.querySelector('input[type="email"]');
            if (!input || !input.value || input.value.indexOf('@') === -1) {
                toast(i18n.error, 'fa-triangle-exclamation');
                if (input) { input.focus(); }
                return;
            }

            toast('Thanks — check your inbox to confirm.', 'fa-envelope-circle-check');
            form.reset();
        });
    }());

    /* ==========================================================
       Reveal on scroll
    ========================================================== */
    (function () {
        var items = qsa('.reveal');
        if (!items.length) { return; }

        if (reduceMotion || !('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        // Fixed px rather than a % — a % margin scales with the viewport and can
        // leave content at the bottom of very tall viewports permanently hidden.
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -60px 0px', threshold: 0.05 });

        items.forEach(function (el) { io.observe(el); });
    }());

    /* ==========================================================
       WooCommerce notices — dismiss button + auto-hide
       Markup comes from Woo core; we only enhance it.
    ========================================================== */
    (function () {
        var notices = qsa('.woocommerce-message, .woocommerce-error, .woocommerce-info');
        if (!notices.length) { return; }

        notices.forEach(function (notice) {
            // The empty cart "info" box is part of the page, not a flash notice.
            if (notice.classList.contains('cart-empty')) { return; }

            var close = document.createElement('button');
            close.type = 'button';
            close.className = 'notice-dismiss';
            close.setAttribute('aria-label', 'Dismiss');
            close.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';

            close.addEventListener('click', function () {
                notice.classList.add('is-leaving');
                setTimeout(function () { notice.remove(); }, 350);
            });

            notice.appendChild(close);

            // Success messages slip away on their own; errors stay until read.
            if (notice.classList.contains('woocommerce-message')) {
                setTimeout(function () {
                    if (document.body.contains(notice)) { close.click(); }
                }, 6000);
            }
        });
    }());

    /* ==========================================================
       Back to top
    ========================================================== */
    (function () {
        var btn = qs('.js-to-top');
        if (!btn) { return; }

        window.addEventListener('scroll', function () {
            btn.classList.toggle('is-visible', window.scrollY > 700);
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    }());

    /* ==========================================================
       404 "Go back" — real history when the visitor came from this
       site, otherwise the link's href (homepage) does its job.
    ========================================================== */
    (function () {
        var back = qs('.js-go-back');
        if (!back) { return; }

        back.addEventListener('click', function (e) {
            var cameFromHere = document.referrer.indexOf(window.location.origin) === 0;

            if (cameFromHere && window.history.length > 1) {
                e.preventDefault();
                window.history.back();
            }
        });
    }());

    /* ==========================================================
       Mobile bar — glass chip glides between tabs (WhatsApp-style)
    ========================================================== */
    (function () {
        var bar = qs('.mobile-bar');
        if (!bar) { return; }

        var glass = document.createElement('span');
        glass.className = 'mobile-bar__glass';
        bar.appendChild(glass);

        function moveTo(item, instant) {
            var b = bar.getBoundingClientRect();
            var e = item.getBoundingClientRect();
            var x = e.left - b.left + (e.width - glass.offsetWidth) / 2;

            if (instant || reduceMotion) { glass.style.transition = 'none'; }
            glass.style.transform = 'translateX(' + x + 'px)';
            glass.classList.add('is-on');

            if (instant || reduceMotion) {
                requestAnimationFrame(function () { glass.style.transition = ''; });
            }
        }

        var active = bar.querySelector('.is-active');
        if (active) { moveTo(active, true); }

        // Tapping glides the chip immediately — the page navigation follows,
        // where the server marks the tab active again.
        bar.addEventListener('click', function (e) {
            var item = e.target.closest('.mobile-bar__item');
            if (item) { moveTo(item); }
        });

        window.addEventListener('resize', function () {
            var current = bar.querySelector('.is-active');
            if (current) { moveTo(current, true); }
        });
    }());

    /* ==========================================================
       Wishlist peek (header heart)
    ========================================================== */
    (function () {
        var btn = qs('.js-wishlist-peek');
        if (!btn) { return; }

        btn.addEventListener('click', function () {
            var count = qs('.js-wish-count');
            var n = count ? parseInt(count.textContent, 10) || 0 : 0;

            toast(
                n ? n + ' item(s) saved to your wishlist.' : 'Your wishlist is empty.',
                'fa-heart'
            );
        });
    }());
}());
