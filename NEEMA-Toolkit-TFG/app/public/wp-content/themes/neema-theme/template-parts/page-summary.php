<?php
/**
 * Template part: Page Summary
 * Muestra un panel lateral con los encabezados h1 y h2 de la página
 */
?>
<div id="page-summary" class="page-summary" style="opacity:0;transition:none;">
    <button id="page-summary-toggle" class="page-summary-toggle" aria-expanded="false" aria-label="Mostrar resumen" style="left:0;transition:none;">
        <svg class="arrow-icon" width="12" height="12" viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" style="transition:none;">
            <path d="M8 4l8 8-8 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <nav class="page-summary-panel" aria-label="Resumen de la página" style="transform:translateX(-100%) translateY(-50%);transition:none;">
        <h3 class="page-summary-title"><?php echo neema_translate('Contenidos de la página'); ?></h3>
        <ul class="page-summary-list"></ul>
    </nav>
</div>

<link rel="stylesheet" href="<?php echo esc_url(get_theme_file_uri('assets/css/page-summary.css')); ?>">

<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('page-summary');
    if (!root) return;
    if (document.body && root.parentNode !== document.body) {
        document.body.appendChild(root);
    }

    var btn = document.getElementById('page-summary-toggle');
    var list = root.querySelector('.page-summary-list');
    root.classList.remove('open');
    root.classList.add('ps-initializing');
    setTimeout(function () {
        root.style.transition = '';
        root.style.opacity = '1';
        btn.style.transition = '';
        btn.style.left = '';

        var arrow = btn.querySelector('.arrow-icon');
        if (arrow) arrow.style.transition = '';

        var panel = root.querySelector('.page-summary-panel');
        if (panel) {
            panel.style.transition = '';
            panel.style.transform = '';
        }

        root.classList.remove('ps-initializing');
    }, 100);

    var isReady = false;
    var justOpened = false;
    var selectors = 'h1, h2, h3';
    var isBuilding = false;

    function isNodeInsideSummary(node) {
        if (!node) return false;
        if (node === root) return true;
        if (node.nodeType === 1) return root.contains(node);
        return !!(node.parentElement && root.contains(node.parentElement));
    }

    function shouldRebuildForMutations(mutations) {
        var i;
        var j;

        for (i = 0; i < mutations.length; i++) {
            var m = mutations[i];
            if (isNodeInsideSummary(m.target)) continue;
            if (m.type === 'attributes') {
                return true;
            }
            if (m.type === 'childList') {
                for (j = 0; j < m.addedNodes.length; j++) {
                    if (!isNodeInsideSummary(m.addedNodes[j])) return true;
                }
                for (j = 0; j < m.removedNodes.length; j++) {
                    if (!isNodeInsideSummary(m.removedNodes[j])) return true;
                }
            }
        }
        return false;
    }

    function toggle(open) {
        if (!isReady) return;

        open = (typeof open === 'boolean') ? open : !root.classList.contains('open');
        root.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');

        if (open) {
            justOpened = true;
            setTimeout(function () {
                justOpened = false;
            }, 400);
        }
    }

    function isVisible(el) {
        if (!el) return false;
        if (el.hasAttribute('hidden')) return false;
        if (el.closest && el.closest('[hidden], [aria-hidden="true"]')) return false;

        var st = window.getComputedStyle(el);
        if (!st) return true;
        if (st.display === 'none' || st.visibility === 'hidden' || st.opacity === '0') return false;

        var rects = el.getClientRects();
        if (!rects || rects.length === 0) return false;

        return true;
    }

    function buildList() {
        isBuilding = true;
        list.innerHTML = '';

        var used = {};
        var headings = document.querySelectorAll(selectors);
        if (!headings || headings.length === 0) {
            headings = document.querySelectorAll('h1,h2,h3');
        }

        Array.prototype.forEach.call(headings, function (h, i) {
            if (!isVisible(h)) return;
            if (h.closest && (h.closest('header') || h.closest('nav') || h.closest('footer') || h.closest('aside') || h.closest('#page-summary'))) return;
            var text = (h.textContent || h.innerText || '').trim();
            if (!text) return;
            var id = h.id && h.id.length
                ? h.id
                : text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            if (!id) id = 'heading-' + i;
            if (used[id]) id = id + '-' + i;
            used[id] = true;
            if (!h.id) h.id = id;
            var li = document.createElement('li');
            var a = document.createElement('a');
            a.href = '#' + id;
            a.textContent = text;
            a.dataset.target = id;
            a.dataset.level = h.tagName.toLowerCase();

            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                ev.stopPropagation();

                var targetId = this.dataset.target;
                var tgt = document.getElementById(targetId);
                
                if (tgt) {
                    toggle(false);
                    setTimeout(function () {
                        var keepXZero = setInterval(function() {
                            if (window.pageXOffset !== 0) {
                                window.scrollTo(0, window.pageYOffset);
                            }
                        }, 10);
                        tgt.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        setTimeout(function() {
                            clearInterval(keepXZero);
                            window.scrollTo(0, window.pageYOffset);
                        }, 1000);
                        
                        setTimeout(function () {
                            try {
                                tgt.focus({ preventScroll: true });
                            } catch (e) {}
                        }, 500);
                        
                        history.replaceState(null, '', '#' + targetId);
                    }, 300);
                }
            });

            li.appendChild(a);
            list.appendChild(li);
        });
        isBuilding = false;
    }

    buildList();

    btn.addEventListener('click', function (e) {
        if (!isReady) return;

        e.preventDefault();
        e.stopPropagation();
        toggle();
    });

    var rebuildTimer = null;

    function scheduleRebuild() {
        if (isBuilding) return;
        clearTimeout(rebuildTimer);
        rebuildTimer = setTimeout(buildList, 300);
    }
    setTimeout(function () {
        isReady = true;
        document.addEventListener('click', function (e) {
            if (justOpened) return;

            if (root.classList.contains('open') && !root.contains(e.target)) {
                toggle(false);
            }
        });
        var mo = new MutationObserver(function (mutations) {
            if (!shouldRebuildForMutations(mutations)) return;
            scheduleRebuild();
        });
        mo.observe(document.body, {
            subtree: true,
            childList: true,
            attributes: true,
            attributeFilter: ['class', 'hidden', 'aria-hidden']
        });

        window.addEventListener('resize', scheduleRebuild);
    }, 300);
});
</script>
