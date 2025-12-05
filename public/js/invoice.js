function invoiceGenerator({ invoice = null, lead = null, packageItems = [] } = {}) {
    return {

        //---------------------------------
        // INITIAL STATE
        //---------------------------------
        selectedPackage: invoice?.package_id ?? "",
        selectedItem: invoice?.package_items_id ?? "",
        selectedRoomType: invoice?.package_type ?? "standard_price",

        packageItems,
        packageData: invoice?.package ?? null,

        basePriceRaw: Number(invoice?.price_per_person ?? 0),

        adultCount: invoice?.adult_count ?? 1,
        childCount: invoice?.child_count ?? 0,

        additionalTravelers: invoice?.additional_travelers ?? "[]",

        primaryName: invoice?.primary_full_name ?? "",
        primaryEmail: invoice?.primary_email ?? "",
        primaryPhone: invoice?.primary_phone ?? "",
        primaryAddress: invoice?.primary_address ?? "",

        discountPercent: Number(invoice?.discount_amount ?? 0),
        taxPercent: Number(invoice?.tax_percent ?? 5),

        travelStartDate: invoice?.travel_start_date ?? new Date().toISOString().slice(0,10),
        additionalDetails: invoice?.additional_details ?? "",

        hiddenFields: {},

        //---------------------------------
        // COMPUTED
        //---------------------------------
        get basePrice() {
            // Editing invoice → use stored price
            if (this.basePriceRaw > 0) return this.basePriceRaw;

            // Selected item + room type → dynamic price
            const selected = this.packageItems.find(i => i.id == this.selectedItem);
            if (!selected) return 0;

            const price = selected[this.selectedRoomType] ?? 0;
            return Number(price);
        },

        get subtotal() {
            return (this.adultCount * this.basePrice) + (this.childCount * (this.basePrice/2));
        },

        get taxAmount() {
            return this.subtotal * (this.taxPercent / 100);
        },

        get finalPrice() {
            const discount = this.subtotal * (this.discountPercent / 100);
            return this.subtotal - discount + this.taxAmount;
        },

        get totalTravelers() {
            let extra = [];
            try {
                extra = Array.isArray(this.additionalTravelers)
                    ? this.additionalTravelers
                    : JSON.parse(this.additionalTravelers || "[]");
            } catch {}
            return this.adultCount + this.childCount + extra.length;
        },

        //---------------------------------
        // LOAD PACKAGE JSON
        //---------------------------------
        loadPackage() {
            if (!this.selectedPackage) return;
            fetch(`/packages/${this.selectedPackage}/json`)
                .then(res => res.json())
                .then(data => {
                    this.packageData = data.package;

                    // Reset item selection if not editing
                    if (!this.basePriceRaw) this.selectedItem = "";

                    this.updateHiddenFields();
                });
        },

        //---------------------------------
        // UPDATE HIDDEN FIELDS
        //---------------------------------
        updateHiddenFields() {
            this.hiddenFields = {
                user_id: document.body.dataset.userId,
                lead_id: lead?.id ?? "",

                package_id: this.selectedPackage,
                package_items_id: this.selectedItem,

                primary_full_name: this.primaryName,
                primary_email: this.primaryEmail,
                primary_phone: this.primaryPhone,
                primary_address: this.primaryAddress,

                adult_count: this.adultCount,
                child_count: this.childCount,
                total_travelers: this.totalTravelers,

                package_name: this.packageData?.package_name ?? "",
                package_type: this.selectedRoomType,

                price_per_person: this.basePrice,
                subtotal_price: this.subtotal.toFixed(2),
                discount_percent: this.discountPercent,
                discount_amount: (this.subtotal * this.discountPercent / 100).toFixed(2),
                tax_percent: this.taxPercent,
                tax_amount: this.taxAmount.toFixed(2),
                final_price: this.finalPrice.toFixed(2),

                travel_start_date: this.travelStartDate,
                issued_date: new Date().toISOString().slice(0,10),
                additional_travelers: this.additionalTravelers,
                additional_details: this.additionalDetails,
            };
        },

        //---------------------------------
        // INITIALIZER
        //---------------------------------
        init() {
            if (this.selectedPackage) this.loadPackage();

            // Watch all reactive properties
            this.$watch(() => ({
                selectedPackage: this.selectedPackage,
                selectedItem: this.selectedItem,
                selectedRoomType: this.selectedRoomType,
                adultCount: this.adultCount,
                childCount: this.childCount,
                primaryName: this.primaryName,
                primaryEmail: this.primaryEmail,
                primaryPhone: this.primaryPhone,
                primaryAddress: this.primaryAddress,
                discountPercent: this.discountPercent,
                taxPercent: this.taxPercent,
                travelStartDate: this.travelStartDate,
                additionalTravelers: this.additionalTravelers,
                additionalDetails: this.additionalDetails
            }), () => this.updateHiddenFields());

            this.updateHiddenFields();
        }
    }
}
