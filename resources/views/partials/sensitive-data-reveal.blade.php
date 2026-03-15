{{-- Modal voor bevestiging van het tonen van gevoelige gegevens --}}
<div id="revealConfirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:8px; padding:30px; max-width:400px; width:90%; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
        <h3 style="margin-top:0;">Gevoelige gegevens</h3>
        <p>Weet je zeker dat je deze gegevens wilt inzien? Deze actie wordt gelogd.</p>
        <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
            <button id="revealConfirmBtn" class="btn btn-orange" style="padding:8px 20px;">Bevestigen</button>
            <button id="revealCancelBtn" class="btn btn-light" style="padding:8px 20px;">Annuleren</button>
        </div>
    </div>
</div>

<style>
.reveal-btn {
    background: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 2px 8px;
    cursor: pointer;
    font-size: 12px;
    color: #555;
    margin-left: 5px;
    transition: background 0.2s;
}
.reveal-btn:hover {
    background: #f0f0f0;
}
.sensitive-value {
    font-family: monospace;
}
</style>

<script>
(function() {
    var pendingRevealBtn = null;
    var revealTimers = [];
    var modal = document.getElementById('revealConfirmModal');
    var confirmBtn = document.getElementById('revealConfirmBtn');
    var cancelBtn = document.getElementById('revealCancelBtn');

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.reveal-btn');
        if (btn) {
            e.preventDefault();
            pendingRevealBtn = btn;
            modal.style.display = 'flex';
        }
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        pendingRevealBtn = null;
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            pendingRevealBtn = null;
        }
    });

    confirmBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        if (!pendingRevealBtn) return;

        var btn = pendingRevealBtn;
        var userUuid = btn.getAttribute('data-user-uuid');
        var fieldType = btn.getAttribute('data-field-type');
        var spanId = btn.getAttribute('data-target');
        var maskedValue = btn.getAttribute('data-masked');
        var span = document.getElementById(spanId);

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) return;

        fetch('{{ route("dashboard.admin.reveal.sensitive.data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                user_uuid: userUuid,
                field_type: fieldType
            })
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Server responded with ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.value) {
                span.textContent = data.value;
                btn.style.display = 'none';

                var timer = setTimeout(function() {
                    span.textContent = maskedValue;
                    btn.style.display = 'inline-block';
                }, 30000);
                revealTimers.push({ timer: timer, span: span, btn: btn, masked: maskedValue });
            }
        })
        .catch(function(err) {
            alert('Kon gegevens niet ophalen. Probeer het opnieuw.');
        });

        pendingRevealBtn = null;
    });

    function remaskAll() {
        revealTimers.forEach(function(item) {
            clearTimeout(item.timer);
            item.span.textContent = item.masked;
            item.btn.style.display = 'inline-block';
        });
        revealTimers = [];
    }

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            remaskAll();
        }
    });

    window.addEventListener('beforeunload', function() {
        remaskAll();
    });
})();
</script>
