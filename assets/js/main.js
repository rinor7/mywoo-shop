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

        var badge = document.createElement('span');
        badge.className = 'toast__icon';

        var i = document.createElement('i');
        i.className = 'fa-solid ' + (icon || 'fa-circle-check');
        badge.appendChild(i);

        var span = document.createElement('span');
        span.className = 'toast__text';
        span.textContent = message;

        el.appendChild(badge);
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
    var lockedScrollY = 0;

    // The position:fixed + top-offset dance below exists purely for iOS
    // Safari: overflow:hidden alone doesn't stop touch-scroll/rubber-band
    // there — a scrollable element inside the overlay (e.g. search results)
    // chains its scroll straight through to the page behind it. Mouse/
    // trackpad devices have no such issue, so they get the plain, boring
    // overflow:hidden lock instead — it has no scroll position to save or
    // restore, so there's no pixel math left to get wrong (no reflow-driven
    // jump on open, nothing to snap-then-scroll-back on close).
    var usesFixedLock = matchMedia('(pointer: coarse)').matches;

    function lockBodyScroll() {
        lockedScrollY = window.scrollY;

        if (!usesFixedLock) {
            document.documentElement.classList.add('is-locked-basic');
            document.body.classList.add('is-locked-basic');
            return;
        }

        // Locking hides the scrollbar (overflow:hidden below), handing its
        // width back to the page. Uncompensated, that extra width reflows
        // text the instant the modal opens — and reflows back on close —
        // which reads as an unrelated "jump" and (on tall pages) briefly
        // pushes content past the locked viewport's clipped bottom edge.
        var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.paddingRight = scrollbarWidth > 0 ? scrollbarWidth + 'px' : '';

        document.body.style.top = ( -lockedScrollY ) + 'px';
        // <html> needs locking too, not just <body> — iOS Safari has been
        // reported to still let a scroll/drag gesture reach the page
        // underneath a fixed overlay when only body is pinned.
        document.documentElement.classList.add('is-locked');
        document.body.classList.add('is-locked');
    }

    function unlockBodyScroll() {
        if (!usesFixedLock) {
            document.documentElement.classList.remove('is-locked-basic');
            document.body.classList.remove('is-locked-basic');
            return;
        }

        // body { scroll-behavior: smooth } (_general.scss) propagates to the
        // window's own scroller, so a plain window.scrollTo() below would
        // animate — visible as a snap to scrollY:0 (the instant is-locked
        // drops and the real, unrestored scroll position shows through)
        // followed by a slow scroll back up to lockedScrollY. Forcing
        // "auto" for this one instant jump is what actually restores the
        // position invisibly; removing the override next frame leaves
        // smooth scrolling intact for real anchor-link navigation.
        var html = document.documentElement;
        var previousScrollBehavior = html.style.scrollBehavior;
        html.style.scrollBehavior = 'auto';

        document.documentElement.classList.remove('is-locked');
        document.body.classList.remove('is-locked');
        document.body.style.top = '';
        document.body.style.paddingRight = '';
        window.scrollTo(0, lockedScrollY);

        requestAnimationFrame(function () {
            html.style.scrollBehavior = previousScrollBehavior;
        });
    }

    function openOverlay(el) {
        if (!el) { return; }

        closeOverlay();
        lastFocus = document.activeElement;
        openEl = el;

        el.hidden = false;
        if (backdrop) { backdrop.hidden = false; }
        lockBodyScroll();

        // Focus synchronously, still inside the click handler's own call
        // stack — mobile browsers only pop the on-screen keyboard for a
        // .focus() that's part of the original tap gesture. Deferring it
        // even one frame (requestAnimationFrame, a timeout) loses that:
        // the field ends up focused in the DOM but keyboard-less until
        // the user taps it again.
        var focusTarget = el.querySelector('.js-search-input') || el.querySelector('button, a, input');
        if (focusTarget) { focusTarget.focus(); }

        requestAnimationFrame(function () {
            el.classList.add('is-open');
            if (backdrop) { backdrop.classList.add('is-open'); }
        });
    }

    function closeOverlay() {
        if (!openEl) { return; }

        var el = openEl;
        openEl = null;

        el.classList.remove('is-open');
        if (backdrop) { backdrop.classList.remove('is-open'); }
        unlockBodyScroll();

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
        } else if (e.target.closest('.js-contact-open')) {
            e.preventDefault();
            openOverlay(qs('.js-contact-modal'));
        } else if (e.target.closest('.js-review-open')) {
            e.preventDefault();
            openOverlay(qs('.js-review-modal'));
        } else if (e.target.closest('.js-drawer-close') || e.target === backdrop) {
            closeOverlay();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeOverlay(); }
    });

    /* ==========================================================
       Live search — search overlay + the inline header bar both
       feed the same myshop_live_search() endpoint, each into their
       own results box.
    ========================================================== */
    function initLiveSearch(input, box) {
        if (!input || !box) { return null; }

        var timer = null;
        var currentTerm = '';

        function reset() {
            box.hidden = true;
            box.innerHTML = '';
        }

        function render(data, term) {
            // A slower, earlier request landing after a newer one — ignore it.
            if (term !== currentTerm) { return; }

            if (!data.products.length) {
                box.innerHTML = '<p class="search-overlay__empty">' + (i18n.noResults || '') + '</p>';
                box.hidden = false;
                return;
            }

            var rows = data.products.map(function (p) {
                return '<li class="search-overlay__item"><a href="' + p.permalink + '">' +
                    '<span class="search-overlay__thumb"><img src="' + p.image + '" alt="" loading="lazy" width="52" height="52"></span>' +
                    '<span class="search-overlay__info">' +
                        '<span class="search-overlay__name">' + p.title + '</span>' +
                        '<span class="search-overlay__price">' + p.price + '</span>' +
                    '</span>' +
                '</a></li>';
            }).join('');

            var more = '';
            if (data.total > data.products.length && data.viewAll) {
                var label = (i18n.viewAllResults || 'View all {count} results').replace('{count}', data.total);
                more = '<a class="search-overlay__more" href="' + data.viewAll + '">' + label + '</a>';
            }

            box.innerHTML = '<ul class="search-overlay__list">' + rows + '</ul>' + more;
            box.hidden = false;
        }

        function search(term) {
            if (!MS.ajaxUrl) { return; }

            fetch(MS.ajaxUrl + '?action=myshop_live_search&term=' + encodeURIComponent(term), {
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json(); })
                .then(function (data) { if (data) { render(data, term); } })
                .catch(function () {});
        }

        input.addEventListener('input', function () {
            var term = input.value.trim();
            currentTerm = term;

            clearTimeout(timer);

            if (term.length < 2) {
                reset();
                return;
            }

            timer = setTimeout(function () { search(term); }, 300);
        });

        return {
            reset: reset,
            clear: function () { input.value = ''; currentTerm = ''; reset(); }
        };
    }

    var overlaySearch = initLiveSearch(qs('.js-search-input'), qs('.js-search-results'));
    var headerSearch = initLiveSearch(qs('.js-header-search-input'), qs('.js-header-search-results'));

    document.addEventListener('click', function (e) {
        if (e.target.closest('.js-search-open')) {
            if (overlaySearch) { overlaySearch.clear(); }
        }

        // Header bar dropdown is always visible (not behind an overlay) —
        // close it on any click outside the pill.
        if (headerSearch && !e.target.closest('.header__search-wrap')) {
            headerSearch.reset();
        }
    });

    // __head is position:sticky (see _drawers.scss) inside the overlay's
    // own scroll — this just adds the shadow once there's actually content
    // scrolled underneath it.
    (function () {
        var scrollArea = qs('.js-search-overlay-scroll');
        var head = qs('.search-overlay__head');
        if (!scrollArea || !head) { return; }

        scrollArea.addEventListener('scroll', function () {
            head.classList.toggle('is-scrolled', scrollArea.scrollTop > 4);
        });

        // The on-screen keyboard is what actually breaks position:fixed/
        // sticky on iOS Safari (confirmed: rotating to landscape, which
        // dismisses the keyboard, makes the exact same layout behave
        // correctly). Dismissing it right as a touch begins — before any
        // drag/scroll motion — lets the keyboard start closing first, so
        // the scroll gesture that follows isn't interrupted mid-way the
        // way blurring on touchmove was. Skipped when the touch starts on
        // the form itself so typing/tapping the input still works normally.
        scrollArea.addEventListener('touchstart', function (e) {
            var active = document.activeElement;
            if (active && 'INPUT' === active.tagName && active.blur && !e.target.closest('.search-overlay__form')) {
                active.blur();
            }
        }, { passive: true });
    }());

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

        // Theme setting (Global Settings → Header Settings → Header Sticky):
        // keep the header pinned at all times instead of hiding on scroll-down.
        var alwaysSticky = header.classList.contains('js-header--always-sticky');

        var lastY = window.scrollY;
        var ticking = false;

        function setHeight() {
            document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
        }

        function onScroll() {
            var y = window.scrollY;

            header.classList.toggle('is-stuck', y > 10);

            if (alwaysSticky) {
                header.classList.remove('is-hidden');
            } else if (!openEl && y > 400) {
                // Only start hiding once well past the fold, and never while an overlay is open.
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
       Mobile bottom nav clearance — measured, not guessed, so any
       fixed bar meant to sit above it (e.g. the PDP sticky add-to-
       cart bar) lines up exactly, including as iOS's
       env(safe-area-inset-bottom) shifts when the browser chrome
       collapses/expands on scroll.
    ========================================================== */
    (function () {
        var mobileBar = qs('.mobile-bar');
        if (!mobileBar) { return; }

        function setClearance() {
            var style = window.getComputedStyle(mobileBar);
            if (style.display === 'none') { return; }
            var bottom = parseFloat(style.bottom) || 0;
            var clearance = bottom + mobileBar.offsetHeight + 14;
            document.documentElement.style.setProperty('--mobile-bar-clear', clearance + 'px');
        }

        window.addEventListener('resize', setClearance);
        window.addEventListener('orientationchange', setClearance);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', setClearance);
        }
        setClearance();
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

        // Shop-by-category mobile carousel. slidesPerView:'auto' (not a
        // fixed ratio like the New arrivals slider above it) because each
        // slide keeps the mosaic's own feature/wide/small width, set in
        // CSS via a shared height + per-variant aspect-ratio.
        if (qs('.js-category-slider')) {
            var categoryProgressBar = qs('.js-category-progress');

            // Same line-progress indicator as the product slider, but its
            // width formula (visible slidesPerView / total slides) assumes
            // every slide is the same width — not true here (slidesPerView
            // is 'auto'). Using the swiper's own measured size vs. its
            // total scrollable width gives the correct visible ratio
            // regardless of how much each slide varies.
            var updateCategoryProgress = function (sw) {
                if (!categoryProgressBar) { return; }

                var ratio = Math.min(1, sw.size / sw.virtualSize);

                categoryProgressBar.style.width = (ratio * 100) + '%';
                categoryProgressBar.style.transform =
                    'translateX(' + (((1 - ratio) / ratio) * 100 * sw.progress) + '%)';
            };

            // freeMode (sticky) instead of strict per-slide snapping: with
            // slides this different in width, a plain snap grid stops at
            // each slide's start — the wide slide alone can be wider than
            // the viewport (needing a second swipe just to clear it), and
            // the last slide's tail can sit past the final snap point
            // entirely (needing an extra swipe to drag it fully into view).
            // freeMode lets one drag/flick travel any distance, all the way
            // to the true scroll end, then settles on the nearest slide.
            swipers.push(new Swiper('.js-category-slider', {
                slidesPerView: 'auto',
                spaceBetween: 14,
                freeMode: {
                    enabled: true,
                    sticky: true,
                    momentumBounce: false
                },
                on: {
                    init: updateCategoryProgress,
                    resize: updateCategoryProgress,
                    progress: updateCategoryProgress
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

            // Products shot with a transparent background (Product Story fields)
            // carry their own gallery background through to every card/quick-view.
            var qvMedia = qs('.js-qv-media', modal);
            if (qvMedia) {
                qvMedia.classList.toggle('quickview__media--custom-bg', !!data.bg);
                qvMedia.style.background = data.bg || '';
            }

            qs('.js-qv-cat', modal).textContent = data.category || '';
            qs('.js-qv-title', modal).textContent = data.name;
            qs('.js-qv-price', modal).innerHTML = data.price;
            qs('.js-qv-excerpt', modal).textContent = data.excerpt || '';
            qs('.js-qv-stars', modal).innerHTML = data.rating ? starsHtml(data.rating) : '';
            qs('.js-qv-count', modal).textContent = data.count ? '(' + data.count + ')' : '';

            // Absent when the "view full details" label (Global Settings → Shop) is cleared.
            var qvLink = qs('.js-qv-link', modal);
            if (qvLink) { qvLink.href = data.url; qvLink.hidden = false; }

            var addBtn = qs('.js-qv-add', modal);
            var addLabel = addBtn.querySelector('span');
            var qtyBox = qs('.qty', modal);

            addBtn.dataset.productId = data.id;
            delete addBtn.dataset.demo;
            delete addBtn.dataset.goto;

            if (data.demo) {
                addBtn.dataset.demo = '1';
                if (addLabel) { addLabel.textContent = i18n.addToCart; }
                if (qtyBox) { qtyBox.hidden = false; }
            } else if (!data.buy) {
                // Needs options — the button already goes to the product page,
                // so a separate "view full details" link would just repeat it.
                addBtn.dataset.goto = data.url;
                if (addLabel) { addLabel.textContent = i18n.chooseOptions; }
                if (qtyBox) { qtyBox.hidden = true; }
                if (qvLink) { qvLink.hidden = true; }
            } else {
                if (addLabel) { addLabel.textContent = i18n.addToCart; }
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
       Product page: qty stepper + sticky add-to-cart bar
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
       Cart page: optional automatic recalculation
       Global Settings → Cart → "Calculate cart totals automatically".

       Debounced so rapid +/- clicks or typing don't fire an AJAX call per
       change; reuses WooCommerce's own `wc_update_cart` event — the same
       one its "Update cart" button relies on — instead of reimplementing
       the AJAX call, so the blocking overlay / error handling stay
       exactly what WooCommerce already ships.

       WooCommerce replaces `.woocommerce-cart-form` wholesale (a fresh,
       visible button included) after every update, so the button has to
       be re-hidden via MutationObserver rather than a one-off call —
       `.myshop-cart` is the nearest ancestor that survives that swap. The
       same observer also syncs the header bubble / off-canvas drawer:
       WooCommerce's own AJAX update only knows about `.woocommerce-cart-form`
       and `.cart_totals` — it has no idea those other two exist, so left
       alone they'd stay stale until a hard refresh.

       Emptying the cart entirely (last item's qty → 0) is a special case:
       WooCommerce's AJAX div-patching needs a `.woocommerce-cart-form__contents`
       element inside a `.woocommerce` wrapper to swap in the empty-cart
       notice, and this theme's markup has neither — the manual "Update cart"
       button only ever looked like it handled this correctly because that
       same missing class makes WooCommerce's click-handler bail out and let
       the browser submit the form for real instead of intercepting it. So
       when a change would empty the cart, submit the form the same way
       instead of dispatching the AJAX event, rather than leaving stale
       cart contents on screen next to a correct-but-orphaned empty notice.
    ========================================================== */
    (function () {
        if (!MS.cartAutoUpdate) { return; }

        var wrapper = qs('.myshop-cart');
        if (!wrapper) { return; }

        function hideUpdateBtn() {
            var btn = wrapper.querySelector('[name="update_cart"]');
            if (btn) { btn.hidden = true; }
        }

        var fragmentTimer = null;
        function syncFragments() {
            clearTimeout(fragmentTimer);
            fragmentTimer = setTimeout(function () {
                post('myshop_refresh_fragments', {}, function (json) {
                    applyFragments(json.fragments);
                });
            }, 300);
        }

        hideUpdateBtn();
        new MutationObserver(function () {
            hideUpdateBtn();
            syncFragments();
        }).observe(wrapper, { childList: true, subtree: true });

        var timer = null;
        wrapper.addEventListener('change', function (e) {
            if (!e.target.classList.contains('qty')) { return; }

            clearTimeout(timer);
            timer = setTimeout(function () {
                var form = wrapper.querySelector('.woocommerce-cart-form');
                var qtys = form ? qsa('input.qty', form) : [];
                var emptying = qtys.length > 0 && qtys.every(function (q) {
                    return (parseFloat(q.value) || 0) <= 0;
                });

                if (emptying && form) {
                    form.submit();
                    return;
                }

                document.body.dispatchEvent(new Event('wc_update_cart', { bubbles: true }));
            }, 600);
        });
    }());

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

        var AUTO_DISMISS_MS = 6000;

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
                var bar = document.createElement('span');
                bar.className = 'notice-progress';
                bar.style.setProperty('--notice-duration', AUTO_DISMISS_MS + 'ms');
                notice.appendChild(bar);
                requestAnimationFrame(function () { bar.classList.add('is-running'); });

                setTimeout(function () {
                    if (document.body.contains(notice)) { close.click(); }
                }, AUTO_DISMISS_MS);
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
       Mobile bar — shrinks after scrolling down a real distance,
       grows back as soon as you reverse and scroll up that same
       distance (not only once you're back at the very top).
    ========================================================== */
    (function () {
        var bar = qs('.mobile-bar');
        if (!bar) { return; }

        var SCROLL_THRESHOLD = 60; // px of continuous scroll before it reacts

        var lastY = window.scrollY;
        var runStartY = lastY; // where the current scroll direction began
        var dir = 0;
        var ticking = false;

        function onScroll() {
            var y = window.scrollY;
            var delta = y - lastY;

            if (delta !== 0) {
                var newDir = delta > 0 ? 1 : -1;
                if (newDir !== dir) {
                    // direction just flipped — measure the next threshold
                    // from here, not from wherever the page started.
                    dir = newDir;
                    runStartY = lastY;
                }
            }

            if (y <= 10) {
                bar.classList.remove('is-compact');
            } else if (dir === 1 && y - runStartY > SCROLL_THRESHOLD) {
                bar.classList.add('is-compact');
            } else if (dir === -1 && runStartY - y > SCROLL_THRESHOLD) {
                bar.classList.remove('is-compact');
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
            // offsetLeft/offsetWidth, not getBoundingClientRect() — the
            // latter reports the VISUALLY RENDERED box, which reflects
            // .mobile-bar's own transform:scale(.8) once .is-compact (see
            // the scroll-shrink block above) is active. glass's own width/
            // translateX are applied in its LOCAL space and then get
            // scaled AGAIN by that same ancestor transform when painted,
            // so measuring from the already-scaled rect double-applies the
            // scale and strands the pill somewhere off its actual tab —
            // only surfaced on real phones because mobile browsers fire a
            // resize event when their toolbar collapses/expands mid-scroll
            // (re-running this while compact), which desktop doesn't do.
            // offsetLeft/offsetWidth read the untransformed layout box, so
            // they're immune to the ancestor's scale either way.
            var w = Math.max(50, item.offsetWidth - 14);
            var x = item.offsetLeft + (item.offsetWidth - w) / 2;

            if (instant || reduceMotion) { glass.style.transition = 'none'; }
            glass.style.width = w + 'px';
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

        // Drag-across-tabs, Instagram-style: sliding a finger from one icon
        // to another glides the chip live as it crosses each tab, instead of
        // only jumping on a plain tap. Pointer Events cover touch + mouse.
        if (window.PointerEvent) {
            var dragItem = null;
            var startItem = null;

            function itemAt(x, y) {
                var el = document.elementFromPoint(x, y);
                return el ? el.closest('.mobile-bar__item') : null;
            }

            bar.addEventListener('pointerdown', function (e) {
                var item = e.target.closest('.mobile-bar__item');
                if (item) { dragItem = item; startItem = item; }
            });

            bar.addEventListener('pointermove', function (e) {
                if (!dragItem) { return; }
                var item = itemAt(e.clientX, e.clientY);
                if (item && item !== dragItem) {
                    dragItem = item;
                    moveTo(item);
                }
            });

            bar.addEventListener('pointerup', function () {
                if (!dragItem) { return; }
                // A plain tap (no movement) already gets a native click of
                // its own — only commit here when the finger actually
                // crossed onto a different tab mid-drag.
                var finalItem = dragItem;
                var moved = finalItem !== startItem;
                dragItem = null;
                startItem = null;
                if (moved) { finalItem.click(); }
            });

            bar.addEventListener('pointercancel', function () {
                dragItem = null;
                startItem = null;
                var current = bar.querySelector('.is-active');
                if (current) { moveTo(current, true); }
            });
        }
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

    /* ==========================================================
       PDP reviews accordion (inside the Specifications section)
    ========================================================== */
    (function () {
        var toggle = qs('.js-reviews-toggle');
        var panel = qs('.js-reviews-panel');
        if (!toggle || !panel) { return; }

        toggle.addEventListener('click', function () {
            var open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!open));
            panel.hidden = open;
        });

        // After submitting a review, WordPress redirects back with
        // #comment-<id> in the hash (and ?unapproved=<id> in the query
        // string if it's held for moderation) — the accordion and the
        // review-modal form itself are both collapsed/hidden by default on
        // a fresh page load, so without this the submitter's own review (or
        // the "awaiting approval" state) would land invisibly.
        var hash = window.location.hash;
        if (/^#comment-\d+$/.test(hash)) {
            var target = document.getElementById(hash.slice(1));
            if (target) {
                panel.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');

                var pending = /[?&]unapproved=\d+/.test(window.location.search);
                toast(pending ? i18n.reviewPending : i18n.reviewPosted, 'fa-comment-dots');

                requestAnimationFrame(function () {
                    target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
                });
            }
        }
    }());

    /* ==========================================================
       YITH wishlist (mobile layout) — move the stock badge onto the
       thumbnail as a corner tag instead of its own "Stock:" row; the
       desktop table's column layout already reads fine as-is.
    ========================================================== */
    (function () {
        qsa('ul.wishlist_table li').forEach(function (li) {
            var badge = li.querySelector('.wishlist-in-stock, .wishlist-out-of-stock');
            var thumb = li.querySelector('.product-thumbnail');
            if (!badge || !thumb) { return; }

            var row = badge.closest('tr');
            var table = row ? row.closest('table.additional-info') : null;
            thumb.appendChild(badge);
            if (row && row.parentNode) { row.parentNode.removeChild(row); }

            // No Quantity row (or anything else) left — drop the now-empty
            // table so its border-top separator doesn't show on its own.
            if (table && !table.querySelector('tr')) {
                table.parentNode.removeChild(table);
            }
        });
    }());
}());
