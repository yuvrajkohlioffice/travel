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

        basePrice: Number(invoice?.price_per_person ?? 0),
        subtotal: 0,
        discountAmount: 0,
        taxAmount: 0,
        finalPrice: 0,
        manualBasePrice: null, // null means "no manual override"

        adultCount: Number(invoice?.adult_count ?? 1),
        childCount: Number(invoice?.child_count ?? 0),

        additionalTravelers: invoice?.additional_travelers
            ? JSON.parse(invoice.additional_travelers)
            : [],

        primaryName: invoice?.primary_full_name ?? "",
        primaryEmail: invoice?.primary_email ?? "",
        primaryPhone: invoice?.primary_phone ?? "",
        primaryAddress: invoice?.primary_address ?? "",

        discountPercent: Number(invoice?.discount_amount ?? 0),
        taxPercent: Number(invoice?.tax_percent ?? 5),

        travelStartDate: invoice?.travel_start_date ?? new Date().toISOString().slice(0, 10),
        additionalDetails: invoice?.additional_details ?? "",

        hiddenFields: {},

        //---------------------------------
        // COMPUTED VALUES
        //---------------------------------
        get taxAmountComputed() {
            return (this.subtotal * this.taxPercent) / 100;
        },

        get totalTravelers() {
            // Primary traveler is always 1
            let extra = [];
            try {
                if (Array.isArray(this.additionalTravelers)) {
                    extra = this.additionalTravelers;
                } else if (typeof this.additionalTravelers === "string" && this.additionalTravelers.trim() !== "") {
                    extra = JSON.parse(this.additionalTravelers);
                }
            } catch (e) {
                console.warn("Invalid additionalTravelers JSON", e);
                extra = [];
            }

            // Total travelers = primary traveler (1) + number of additional travelers
            return 1 + extra.length;
        },

        canAddTraveler() {
            // Example: maximum 10 travelers
            return this.totalTravelers < 10;
        },


        //---------------------------------
        // DATA NORMALIZATION
        //---------------------------------
        normalizeTravelers() {
            if (!Array.isArray(this.additionalTravelers)) {
                try {
                    this.additionalTravelers = this.additionalTravelers
                        ? JSON.parse(this.additionalTravelers)
                        : [];
                } catch (e) {
                    console.warn("Invalid additionalTravelers JSON:", e);
                    this.additionalTravelers = [];
                }
            }
        },

        //---------------------------------
        // PACKAGE DATA LOADING
        //---------------------------------
        async loadPackage() {
            if (!this.selectedPackage) return;

            try {
                const res = await fetch(`/packages/${this.selectedPackage}/json`);
                const data = await res.json();

                this.packageData = data.package;
                this.packageItems = data.package.packageItems || [];

                // Reset selections when creating new invoice
                if (!invoice) {
                    this.selectedItem = "";
                    this.selectedRoomType = "standard_price";
                }

                this.recalculate();
            } catch (error) {
                console.error("Failed to load package:", error);
            }
        },

        //---------------------------------
        // TRAVELER MANAGEMENT
        //---------------------------------
        addTraveler() {
            this.additionalTravelers.push({ name: "", relation: "", age: "" });
            this.adultCount = Number(this.adultCount) - 1; // or adjust logic if child
            this.recalculate();
        },

        recalculateAdditionalTravelers() {
            const primaryTraveler = 1; // primary always exists

            // Extra adults and children
            const extraAdults = Math.max(0, this.adultCount - primaryTraveler);
            const extraChildren = Math.max(0, this.childCount);

            let travelers = [];

            // Add extra adult travelers
            for (let i = 0; i < extraAdults; i++) {
                travelers.push({ name: "", relation: "Adult", age: "" });
            }

            // Add child travelers
            for (let i = 0; i < extraChildren; i++) {
                travelers.push({ name: "", relation: "Child", age: "" });
            }

            // Keep additionalTravelers synced with adult + child counts
            this.additionalTravelers = travelers;
        },



        removeTraveler(index) {
            this.additionalTravelers.splice(index, 1);
            this.recalculate();
        },

        //---------------------------------
        // PRICE CALCULATIONS
        //---------------------------------
        calculateSubtotal() {
            const adultTotal = Number(this.adultCount) * this.basePrice;
            const childTotal = Number(this.childCount) * (this.basePrice / 2);
            this.subtotal = adultTotal + childTotal;
        },

        calculateDiscount() {
            this.discountAmount = (this.subtotal * this.discountPercent) / 100;
        },

        calculateTax() {
            this.taxAmount = (this.subtotal * this.taxPercent) / 100;
        },

        calculateFinalPrice() {
            this.finalPrice = Math.max(0, this.subtotal - this.discountAmount + this.taxAmount);
        },

        /**
         * Updates all prices in a proper order
         * Handles subtotal, discount, tax, and final price
         */
        updatePrices() {
            const item = this.packageItems.find(i => String(i.id) === String(this.selectedItem));
            const defaultPrice = item ? Number(item[this.selectedRoomType] ?? 0) : 0;

            // If manual price is set, use it; otherwise, use package item price
            this.basePrice = this.manualBasePrice !== null ? this.manualBasePrice : defaultPrice;

            this.calculateSubtotal();  // adults full, children half
            this.calculateDiscount();
            this.calculateTax();
            this.calculateFinalPrice();
        },



        //---------------------------------
        // HIDDEN FORM FIELDS
        //---------------------------------
        updateHiddenFields() {
            this.hiddenFields = {
                user_id: document.body.dataset.userId,
                lead_id: lead?.id ?? "",

                package_id: this.selectedPackage,
                package_items_id: this.selectedItem,
                package_type: this.selectedRoomType,
                package_name: this.packageData?.package_name ?? "",

                primary_full_name: this.primaryName,
                primary_email: this.primaryEmail,
                primary_phone: this.primaryPhone,
                primary_address: this.primaryAddress,

                adult_count: this.adultCount,
                child_count: this.childCount,
                total_travelers: this.totalTravelers,

                price_per_person: this.basePrice,
                subtotal_price: this.subtotal.toFixed(2),
                discount_percent: this.discountPercent,
                discount_amount: this.discountAmount.toFixed(2),
                tax_percent: this.taxPercent,
                tax_amount: this.taxAmount.toFixed(2),
                final_price: this.finalPrice.toFixed(2),

                travel_start_date: this.travelStartDate,
                issued_date: new Date().toISOString().slice(0, 10),
                additional_travelers: JSON.stringify(this.additionalTravelers),
                additional_details: this.additionalDetails,
            };
        },

        //---------------------------------
        // RECOMPUTE EVERYTHING
        //---------------------------------
        recalculate() {
            this.normalizeTravelers();
            this.updatePrices();
            this.recalculateAdditionalTravelers();
            this.updateHiddenFields();
        },

        //---------------------------------
        // INITIALIZER
        //---------------------------------
        init() {
            this.normalizeTravelers();
            this.recalculate();

            if (this.selectedPackage) this.loadPackage();

            // Watch changes in selected item and room type
            this.$watch('selectedItem', (newVal) => {
                this.manualBasePrice = null; // reset manual price on item change
                this.updatePrices();
            });
            // Watch changes in adult/child count
            this.$watch(() => this.adultCount, () => {
                this.recalculate();
            });
            this.$watch(() => this.childCount, () => {
                this.recalculate();
            });

            this.$watch('selectedRoomType', (newVal) => {
                this.manualBasePrice = null; // reset manual price on room type change
                this.updatePrices();
            });


            // Global watcher for reactive updates
            this.$watch(() => ({
                adultCount: this.adultCount,
                childCount: this.childCount,
                discountPercent: this.discountPercent,
                taxPercent: this.taxPercent,
                travelStartDate: this.travelStartDate,
                primaryName: this.primaryName,
                primaryEmail: this.primaryEmail,
                primaryPhone: this.primaryPhone,
                primaryAddress: this.primaryAddress,
                additionalTravelers: this.additionalTravelers,
                additionalDetails: this.additionalDetails,
            }), () => {
                this.updatePrices();
                this.updateHiddenFields();
            });
        },

        //---------------------------------
        // UTILITY FUNCTIONS
        //---------------------------------
        formatCurrency(value) {
            return Number(value).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
    };
}
