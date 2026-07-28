(function () {
    function onReady(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    onReady(function () {
        if (typeof FingerprintJS === 'undefined') {
            console.warn('[visitor-logger] FingerprintJS is not loaded. Fingerprinting will be skipped.');
            return;
        }

        var route = (typeof visitorLoggerRoute !== 'undefined' && visitorLoggerRoute)
            ? visitorLoggerRoute
            : '/log-fingerprint';

        var csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        FingerprintJS.load()
            .then(function (fp) { return fp.get(); })
            .then(function (result) {
                return fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ fingerprint: result.visitorId }),
                });
            })
            .catch(function (err) {
                console.warn('[visitor-logger] Failed to send fingerprint.', err);
            });
    });
}());
