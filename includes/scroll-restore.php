<?php

/**
 * Restaura el scroll solo tras enviar un formulario en la misma sección.
 * Al cambiar de página por el menú, la vista empieza arriba.
 */
function bengalaScrollRestoreHead(): void {
    ?>
<script>
history.scrollRestoration = 'manual';
</script>
    <?php
}

function bengalaScrollRestoreFooter(): void {
    ?>
<script>
(function () {
    var PENDING = 'bengala_restore_pending';
    var PREFIX = 'bengala_scroll:';

    var FLASH_PARAMS = [
        'status', 'updated', 'deleted', 'error', 'enviado', 'aprobado', 'rechazado',
        'prestado', 'devuelto', 'success', 'saved', 'n', 'game_error'
    ];

    function normalizeKey(href) {
        try {
            var u = new URL(href, location.href);
            FLASH_PARAMS.forEach(function (p) {
                u.searchParams.delete(p);
            });
            return u.pathname + u.search;
        } catch (e) {
            return location.pathname;
        }
    }

    function currentKey() {
        return normalizeKey(location.href);
    }

    window.__bengalaScrollKey = currentKey;

    function restoreIfPending() {
        var cur = currentKey();
        var pending = sessionStorage.getItem(PENDING);
        if (!pending || pending !== cur) {
            sessionStorage.removeItem(PENDING);
            window.scrollTo(0, 0);
            return;
        }
        var saved = sessionStorage.getItem(PREFIX + cur);
        sessionStorage.removeItem(PENDING);
        if (saved === null) {
            return;
        }
        var y = parseInt(saved, 10);
        if (!isNaN(y) && y > 0) {
            window.scrollTo(0, y);
        }
    }

    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download')) {
            return;
        }
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) {
            return;
        }
        try {
            var dest = new URL(href, location.href);
            if (dest.origin !== location.origin) {
                return;
            }
            sessionStorage.removeItem(PENDING);
        } catch (err) {}
    }, true);

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') {
            return;
        }
        var method = (form.method || 'get').toLowerCase();
        var returnPath = form.getAttribute('data-scroll-return');
        if (method !== 'post' && !returnPath) {
            return;
        }
        var targetKey = returnPath ? normalizeKey(returnPath) : currentKey();
        try {
            sessionStorage.setItem(PREFIX + targetKey, String(window.scrollY));
            sessionStorage.setItem(PENDING, targetKey);
        } catch (err) {}
    }, true);

    window.addEventListener('load', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                restoreIfPending();
                setTimeout(restoreIfPending, 50);
            });
        });
    });
})();
</script>
    <?php
}
