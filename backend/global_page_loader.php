<style>
#global-page-loader {
    position: fixed;
    inset: 0;
    z-index: 100000;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(15, 30, 60, 0.38);
    backdrop-filter: blur(3px);
}

#global-page-loader.show {
    display: flex;
}

#global-page-loader .loader-card {
    min-width: 260px;
    border-radius: 14px;
    border: 1px solid #dbe3ef;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 18px 42px rgba(15, 30, 60, 0.22);
    padding: 14px 18px;
    text-align: center;
}

#global-page-loader .loader-spinner {
    width: 30px;
    height: 30px;
    border: 3px solid #d7e5fb;
    border-top-color: #0f1e3c;
    border-radius: 999px;
    margin: 0 auto 10px;
    animation: global-page-loader-spin 0.9s linear infinite;
}

#global-page-loader .loader-text {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.01em;
}

@keyframes global-page-loader-spin {
    to { transform: rotate(360deg); }
}
</style>

<div id="global-page-loader" aria-hidden="true">
    <div class="loader-card" role="status" aria-live="polite">
        <div class="loader-spinner"></div>
        <p id="global-page-loader-text" class="loader-text">Memuat halaman...</p>
    </div>
</div>

<script>
(function () {
    const loader = document.getElementById('global-page-loader');
    const textEl = document.getElementById('global-page-loader-text');
    if (!loader) return;

    let shown = false;

    function showLoader(message) {
        if (shown) return;
        if (textEl && message) {
            textEl.textContent = message;
        }
        loader.classList.add('show');
        shown = true;
    }

    function hideLoader() {
        loader.classList.remove('show');
        shown = false;
        if (textEl) {
            textEl.textContent = 'Memuat halaman...';
        }
    }

    function shouldIgnoreLink(anchor) {
        if (!anchor) return true;
        if (anchor.classList.contains('js-logout-trigger') || anchor.hasAttribute('data-logout-url')) return true;
        const href = (anchor.getAttribute('href') || '').trim();
        if (!href) return true;
        if (href.startsWith('#')) return true;
        if (href.toLowerCase().startsWith('javascript:')) return true;
        if (anchor.hasAttribute('download')) return true;
        if ((anchor.getAttribute('target') || '').toLowerCase() === '_blank') return true;
        return false;
    }

    document.addEventListener('click', function (event) {
        const anchor = event.target.closest('a');
        if (shouldIgnoreLink(anchor)) return;

        const href = anchor.getAttribute('href') || '';
        if (href.includes('logout.php')) {
            showLoader('Memproses logout...');
        } else {
            showLoader('Membuka halaman...');
        }
    }, true);

    document.addEventListener('submit', function () {
        showLoader('Menyimpan data...');
    }, true);

    window.addEventListener('beforeunload', function () {
        showLoader('Memuat halaman...');
    });

    // Saat kembali via back/forward (BFCache), pastikan loader tidak tersisa.
    window.addEventListener('pageshow', function () {
        hideLoader();
    });

    // Safety net untuk load normal.
    window.addEventListener('load', function () {
        hideLoader();
    });

    window.showGlobalPageLoader = showLoader;
    window.hideGlobalPageLoader = hideLoader;
})();
</script>
