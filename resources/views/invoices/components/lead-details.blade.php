@if ($lead)
<div class="bg-white rounded-xl p-4 shadow">
    <p class="font-semibold">Lead: {{ $lead->name }}</p>
    <p>Email: {{ $lead->email ?? '---' }}</p>
    <p>Phone: {{ $lead->phone_code . $lead->phone_number ?? '---' }}</p>
    <p>Address: {{ $lead->address ?? '---' }}</p>
    
    <div class="mt-3">
        <button type="button" onclick="sendPortalLink(this, {{ $lead->id }}, '{{ $lead->email }}')" 
        class="flex items-center gap-2 bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-md active:scale-95">
    
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 icon-default" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
    
    <svg class="animate-spin h-4 w-4 text-white icon-loading hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    <span class="btn-text">Send Portal Link Via Mail</span>
</button>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function sendPortalLink(button, leadId, leadEmail) {
        
        // 1. Confirm First
        Swal.fire({
            title: 'Send Access Link?',
            text: `Are you sure you want to send the portal link to ${leadEmail}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#9333ea', // Purple to match your theme
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performAjaxRequest(button, leadId);
            }
        });
    }

    function performAjaxRequest(button, leadId) {
        // UI Elements
        const textSpan = button.querySelector('.btn-text');
        const iconDefault = button.querySelector('.icon-default');
        const iconLoading = button.querySelector('.icon-loading');

        // 2. Set Loading State
        button.disabled = true;
        textSpan.innerText = 'Sending...';
        iconDefault.classList.add('hidden');
        iconLoading.classList.remove('hidden');

        // 3. Make Request
        fetch(`/leads/send-access/${leadId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 4. Success Alert
                Swal.fire({
                    title: 'Sent!',
                    text: data.message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                // 5. Logical Error (e.g. No Email)
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            // 6. System Error
            console.error('Error:', error);
            Swal.fire({
                title: 'System Error',
                text: 'Something went wrong. Please check console or logs.',
                icon: 'error'
            });
        })
        .finally(() => {
            // 7. Reset Button State
            button.disabled = false;
            textSpan.innerText = 'Send Portal Link';
            iconDefault.classList.remove('hidden');
            iconLoading.classList.add('hidden');
        });
    }
</script>