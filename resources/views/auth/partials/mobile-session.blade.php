<script>
(function () {
    const meta = document.querySelector('meta[name="csrf-token"]');

    async function refreshCsrf() {
        try {
            const res = await fetch('{{ url('/csrf-refresh') }}', {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'Accept': 'application/json' },
            });
            if (!res.ok) return false;
            const data = await res.json();
            if (!data?.token) return false;
            if (meta) meta.setAttribute('content', data.token);
            document.querySelectorAll('input[name="_token"]').forEach(function (el) {
                el.value = data.token;
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            refreshCsrf();
        }
    });

    document.querySelectorAll('form[method="POST"]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.authSubmitting === '1') {
                event.preventDefault();
                return;
            }

            event.preventDefault();
            form.dataset.authSubmitting = '1';

            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            refreshCsrf().finally(function () {
                form.dataset.authSubmitting = '0';
                form.submit();
            });
        });
    });
})();
</script>
