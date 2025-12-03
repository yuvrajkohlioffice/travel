function shareModal(packages) {
    return {
        shareOpen: false,
        shareLeadId: '',
        shareLeadName: '',
        selectedPackage: '',
        showDropdown: false,
        showSelectedPackage: false,
        selectedPackageName: '',
        allPackages: packages,

        handleShare(id, name, packageId = null) {
            this.shareLeadId = id;
            this.shareLeadName = name;

            if (packageId) {
                this.selectedPackage = packageId;
                this.showDropdown = false;
                this.showSelectedPackage = true;

                const pkg = this.allPackages.find(p => p.id == packageId);
                this.selectedPackageName = pkg ? pkg.package_name : 'Unknown Package';

                this.shareOpen = true;
                return;
            }

            this.showSelectedPackage = false;
            this.showDropdown = true;
            this.shareOpen = true;

            if (this.allPackages.length > 0) {
                this.selectedPackage = this.allPackages[0].id;
            }
        },

        closeShare() {
            this.shareOpen = false;
        },

        sendEmail() {
            // Implement email logic here
            alert(`Email sent for Lead ID: ${this.shareLeadId}`);
        },

        sendWhatsApp() {
            // Implement WhatsApp logic here
            alert(`WhatsApp sent for Lead ID: ${this.shareLeadId}`);
        }
    }
}

// Attach to window
window.shareModal = shareModal;
