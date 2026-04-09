(function (window) {
    const triggeredKeys = new Set();

    function showAlert(title, message, type) {
        if (typeof window.showNotification === 'function') {
            const result = window.showNotification(title, message, type || 'info');
            if (result && typeof result.then === 'function') {
                return result;
            }
        }

        window.alert(`${title}\n\n${message}`);
        return Promise.resolve();
    }

    function normalizeKey(key) {
        return key || 'default';
    }

    window.TestTimerAlert = {
        warn(options) {
            const settings = options || {};
            const key = normalizeKey(settings.key);
            const remaining = Number(settings.remaining);
            const threshold = Number.isFinite(Number(settings.threshold)) ? Number(settings.threshold) : 300;

            if (!Number.isFinite(remaining)) {
                return false;
            }

            if (remaining > threshold) {
                triggeredKeys.delete(key);
                return false;
            }

            if (triggeredKeys.has(key)) {
                return false;
            }

            triggeredKeys.add(key);
            showAlert(
                settings.title || 'Waktu Hampir Habis',
                settings.message || 'Sisa waktu tes tinggal sedikit. Periksa jawaban Anda sekarang.',
                settings.type || 'info'
            );
            return true;
        },
        reset(key) {
            if (typeof key === 'undefined') {
                triggeredKeys.clear();
                return;
            }

            triggeredKeys.delete(normalizeKey(key));
        }
    };
})(window);
