<?php

/**
 * Restaura el scroll al volver tras formularios o recargas con mensajes (?status=, etc.).
 */
function bengalaScrollRestoreHead(): void {
    ?>
<script>
history.scrollRestoration = 'manual';
(function () {
    function storageKey() {
        try {
            var u = new URL(location.href);
            ['status', 'updated', 'deleted', 'error', 'enviado', 'aprobado', 'rechazado',
             'prestado', 'devuelto', 'success', 'saved', 'n', 'game_error'].forEach(function (p) {
                u.searchParams.delete(p);
            });
            return 'bengala_scroll:' + u.pathname + u.search;
        } catch (e) {
            return 'bengala_scroll:' + location.pathname;
        }
    }
    window.__bengalaScrollKey = storageKey;
    var saved = sessionStorage.getItem(storageKey());
    if (saved !== null) {
        var y = parseInt(saved, 10);
        if (!isNaN(y) && y > 0) {
            window.scrollTo(0, y);
        }
    }
})();
</script>
    <?php
}

function bengalaScrollRestoreFooter(): void {
    ?>
<script>
(function () {
    function key() {
        return typeof window.__bengalaScrollKey === 'function'
            ? window.__bengalaScrollKey()
            : 'bengala_scroll:' + location.pathname;
    }
    function save() {
        try {
            sessionStorage.setItem(key(), String(window.scrollY));
        } catch (e) {}
    }
    var timer;
    window.addEventListener('scroll', function () {
        clearTimeout(timer);
        timer = setTimeout(save, 100);
    }, { passive: true });
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.method || form.method.toLowerCase() !== 'post') {
            return;
        }
        save();
        var ret = form.getAttribute('data-scroll-return');
        if (ret) {
            try {
                sessionStorage.setItem('bengala_scroll:' + ret, String(window.scrollY));
            } catch (err) {}
        }
    }, true);
    window.addEventListener('pagehide', save);
    function restoreOnce() {
        var saved = sessionStorage.getItem(key());
        if (saved === null) {
            return;
        }
        var y = parseInt(saved, 10);
        if (!isNaN(y) && y > 0) {
            window.scrollTo(0, y);
        }
    }
    window.addEventListener('load', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                restoreOnce();
                setTimeout(restoreOnce, 50);
            });
        });
    });
})();
</script>
    <?php
}
