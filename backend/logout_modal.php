<!-- Reusable Logout Confirmation Modal -->
<div id="logoutConfirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-start gap-3">
            <div class="mt-0.5 grid h-10 w-10 place-items-center rounded-xl bg-amber-100 text-lg">⚠️</div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Konfirmasi Logout</h3>
                <p class="mt-1 text-sm text-slate-600">Anda yakin ingin keluar dari sesi saat ini?</p>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="button" id="logoutCancelBtn" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Batal
            </button>
            <a id="logoutConfirmBtn" href="logout.php" class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-red-700">
                Ya, Logout
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('logoutConfirmModal');
    const confirmBtn = document.getElementById('logoutConfirmBtn');
    const cancelBtn = document.getElementById('logoutCancelBtn');
    if (!modal || !confirmBtn || !cancelBtn) return;

    let targetUrl = 'logout.php';

    function openModal(url) {
        targetUrl = url || 'logout.php';
        confirmBtn.setAttribute('href', targetUrl);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.js-logout-trigger');
        if (!trigger) return;

        event.preventDefault();
        openModal(trigger.getAttribute('data-logout-url') || trigger.getAttribute('href'));
    });

    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });
})();
</script>
