function invoiceModal(packages) {
    return {
        open: false,
        leadId: "",
        leadName: "",
        packages: packages,
        selectedPackage: "",
        packageData: null,

        openInvoiceModal(id, name) {
            this.leadId = id;
            this.leadName = name;
            this.open = true;
        },

        async fetchPackageData() {
            if (!this.selectedPackage) return;

            let res = await fetch(`/packages/${this.selectedPackage}/details`);
            this.packageData = await res.json();
        },

        async sendInvoice() {
            if (!this.selectedPackage) {
                alert("Please select a package!");
                return;
            }

            await fetch(`/send-invoice`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    lead_id: this.leadId,
                    package_id: this.selectedPackage
                })
            });

            alert("Invoice Sent Successfully!");
            this.open = false;
        }
    }
}

// Attach to window
window.invoiceModal = invoiceModal;
