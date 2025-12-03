function followupModal() {
    return {
        /* ---------------- FOLLOW-UP MODAL ---------------- */
        open: false,
        leadId: '',
        leadName: '',
        phoneNumber: '',
        phoneCode: '',
        fullNumber: '',
        selectedReason: '',
        followups: [],
        reasons: [
            'Call Back Later', 'Call Me Tomorrow', 'Payment Tomorrow', 'Talk With My Partner',
            'Work with other company', 'Not Interested', 'Interested', 'Wrong Information',
            'Not Pickup', 'Other'
        ],

        openFollowModal(id, name) {
            this.leadId = id;
            this.leadName = name;
            this.open = true;

            fetch(`/leads/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    this.phoneNumber = data.phone.phone_number;
                    this.phoneCode = data.phone.phone_code;
                    this.fullNumber = data.phone.full_number;

                    this.followups = data.followups.map(f => ({
                        ...f,
                        created_at: f.created_at,
                        next_followup_date: f.next_followup_date,
                        user_name: f.user_name
                    }));
                });
        },

        close() {
            this.open = false;
        },
    }
}

// Attach to window for Alpine.js
window.followupModal = followupModal;
