<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">


        <!-- Success message -->
        @if (session('success'))
            <div class="m-6 p-4 bg-green-500 text-white rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }

                .print-break {
                    page-break-before: always;
                }
            }

            body {
                font-family: 'Poppins', sans-serif;
            }
        </style>

        <div class="max-w-6xl mx-auto p-4 md:p-6 lg:p-8">
            <!-- Print Button -->
            <div class="no-print flex justify-end mb-6">
                <button onclick="window.print()"
                    class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-print mr-3"></i> Print Invoice
                </button>
            </div>

            <!-- Invoice Container -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-blue-100">
                <!-- Header Section -->
                <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex items-center mb-8 md:mb-0">
                            <!-- Company Logo -->
                            <div
                                class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mr-5 shadow-lg">
                                <i class="fas fa-plane-departure text-4xl text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold text-gray-900">Wanderlust Adventures</h1>
                                <div class="flex flex-wrap items-center mt-2">
                                    <p class="text-gray-600 mr-4">
                                        <i class="fas fa-envelope mr-2 text-blue-500"></i>bookings@wanderlust.com
                                    </p>
                                    <p class="text-gray-600">
                                        <i class="fas fa-phone mr-2 text-blue-500"></i>+1 (800) 555-TRAVEL
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <h2 class="text-4xl font-bold text-gray-900 uppercase">Travel Invoice</h2>
                            <div class="mt-4 space-y-2 bg-white p-4 rounded-xl shadow-sm inline-block">
                                <p class="font-semibold"><span class="text-blue-600">Invoice #:</span> TRAV-2023-0876
                                </p>
                                <p class="font-semibold"><span class="text-blue-600">Issue Date:</span> Nov 20, 2023</p>
                                <p class="font-semibold"><span class="text-green-600">Travel Start:</span> Dec 15, 2023
                                </p>
                                <p class="font-semibold"><span class="text-amber-600">Travel End:</span> Dec 25, 2023
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-tag mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> Traveler
                        Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div
                            class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 shadow-sm border border-blue-100">
                            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-3 text-blue-500"></i> Primary Traveler
                            </h4>
                            <div class="space-y-3">
                                <p class="text-gray-800"><span class="font-semibold">Full Name:</span> Michael Anderson
                                </p>
                                <p class="text-gray-800"><span class="font-semibold">Email:</span>
                                    michael.anderson@email.com</p>
                                <p class="text-gray-800"><span class="font-semibold">Phone:</span> +1 (555) 789-0123</p>
                                <p class="text-gray-800"><span class="font-semibold">Address:</span> 456 Park Avenue,
                                    New York, NY 10022, USA</p>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-cyan-50 to-white rounded-2xl p-6 shadow-sm border border-cyan-100">
                            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-users mr-3 text-cyan-500"></i> Additional Travelers
                            </h4>
                            <div class="space-y-2">
                                <p class="text-gray-800"><span class="font-semibold">Sarah Anderson</span> (Spouse)</p>
                                <p class="text-gray-800"><span class="font-semibold">Emma Anderson</span> (Child, Age 8)
                                </p>
                                <p class="text-gray-800"><span class="font-semibold">Liam Anderson</span> (Child, Age 5)
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-cyan-100">
                                <p class="text-gray-800"><span class="font-semibold">Total Travelers:</span> 4 Persons
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Travel Package Details Table -->
                <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                        <i class="fas fa-suitcase-rolling mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> Travel
                        Package Details
                    </h3>

                    <div class="overflow-x-auto rounded-2xl border border-blue-100 shadow-sm">
                        <table class="min-w-full divide-y divide-blue-100">
                            <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Package Name</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Destination</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Travel Dates</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Travelers</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Accommodation</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Transport</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Price/Person</th>
                                    <th
                                        class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-50">
                                <!-- Row 1 -->
                                <tr class="hover:bg-blue-50 transition-colors duration-200">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div>
                                            <p class="font-bold text-gray-900 text-lg">Bali Paradise Escape</p>
                                            <div class="text-gray-600 text-sm mt-1">
                                                <p><i class="fas fa-utensils text-green-500 mr-2"></i> All Meals
                                                    Included</p>
                                                <p><i class="fas fa-binoculars text-amber-500 mr-2"></i> 5 Sightseeing
                                                    Tours</p>
                                                <p><i class="fas fa-swimming-pool text-blue-500 mr-2"></i> Infinity Pool
                                                    Access</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                            <span class="font-medium">Bali, Indonesia</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <p class="font-medium">Dec 15 - Dec 25, 2023</p>
                                        <p class="text-sm text-gray-600">10 Days / 9 Nights</p>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <p class="font-bold text-lg text-center">4</p>
                                        <p class="text-sm text-gray-600 text-center">(2 Adults, 2 Kids)</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <i class="fas fa-hotel text-purple-500 mr-2"></i>
                                            <span>Luxury Beach Resort</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Ocean View Villa</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col space-y-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-plane text-blue-500 mr-2"></i>
                                                <span>Flight (Business)</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-car text-gray-500 mr-2"></i>
                                                <span>Private Transfers</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap font-bold text-gray-900">$1,850.00</td>
                                    <td class="px-6 py-5 whitespace-nowrap font-bold text-lg text-blue-700">$7,400.00
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr class="hover:bg-blue-50 transition-colors duration-200">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div>
                                            <p class="font-bold text-gray-900 text-lg">European Highlights</p>
                                            <div class="text-gray-600 text-sm mt-1">
                                                <p><i class="fas fa-utensils text-green-500 mr-2"></i> Breakfast
                                                    Included</p>
                                                <p><i class="fas fa-binoculars text-amber-500 mr-2"></i> Guided City
                                                    Tours</p>
                                                <p><i class="fas fa-museum text-purple-500 mr-2"></i> Museum Passes</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                            <span class="font-medium">Paris & Rome</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <p class="font-medium">Dec 26 - Jan 3, 2024</p>
                                        <p class="text-sm text-gray-600">9 Days / 8 Nights</p>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <p class="font-bold text-lg text-center">4</p>
                                        <p class="text-sm text-gray-600 text-center">(2 Adults, 2 Kids)</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <i class="fas fa-hotel text-purple-500 mr-2"></i>
                                            <span>4-Star City Hotels</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col space-y-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-plane text-blue-500 mr-2"></i>
                                                <span>Flight (Economy)</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-train text-gray-700 mr-2"></i>
                                                <span>High-Speed Train</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap font-bold text-gray-900">$1,250.00</td>
                                    <td class="px-6 py-5 whitespace-nowrap font-bold text-lg text-blue-700">$5,000.00
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
                    <div class="flex flex-col lg:flex-row justify-between">
                        <!-- Payment Information & QR -->
                        <div class="lg:w-2/3 mb-10 lg:mb-0">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-credit-card mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i>
                                Payment Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <!-- QR Code -->
                                <div
                                    class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm">
                                    <h4 class="font-bold text-gray-900 mb-4 text-center">Scan to Pay</h4>
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="h-48 w-48 border-2 border-dashed border-blue-300 rounded-2xl flex items-center justify-center bg-white mb-4">
                                            <div class="text-center">
                                                <i class="fas fa-qrcode text-6xl text-blue-400 mb-3"></i>
                                                <p class="text-sm text-gray-600">Payment QR Code</p>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 text-center">Use any mobile banking app to scan
                                            and pay</p>
                                    </div>
                                </div>

                                <!-- Amount in Words -->
                                <div
                                    class="bg-gradient-to-br from-cyan-50 to-white rounded-2xl p-6 border border-cyan-100 shadow-sm">
                                    <h4 class="font-bold text-gray-900 mb-4">Amount in Words</h4>
                                    <div class="bg-white p-4 rounded-xl border border-cyan-200">
                                        <p class="text-gray-800 italic">
                                            <i class="fas fa-file-invoice-dollar text-cyan-500 mr-2"></i>
                                            Thirteen thousand four hundred seventy-two and 00/100 US Dollars Only
                                        </p>
                                    </div>
                                    <div class="mt-6">
                                        <h5 class="font-bold text-gray-900 mb-2">Payment Methods</h5>
                                        <div class="flex space-x-4">
                                            <div class="bg-white p-3 rounded-lg border">
                                                <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border">
                                                <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border">
                                                <i class="fab fa-cc-paypal text-2xl text-blue-500"></i>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border">
                                                <i class="fas fa-university text-2xl text-green-600"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pay Now Button -->
                            <div class="no-print">
                                <button id="payNowBtn"
                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg flex items-center justify-center text-lg transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-lock mr-3"></i> Secure Payment Portal
                                </button>
                            </div>
                        </div>

                        <!-- Summary Box -->
                        <div class="lg:w-1/3">
                            <div
                                class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl p-8 text-white shadow-2xl">
                                <h3 class="text-2xl font-bold mb-8 text-center border-b border-blue-700 pb-4">Invoice
                                    Summary</h3>

                                <div class="space-y-4 mb-8">
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-100">Package Subtotal</span>
                                        <span class="font-bold">$12,400.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-100">Early Bird Discount</span>
                                        <span class="font-bold text-green-300">-$620.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-100">Taxes & Service Fees</span>
                                        <span class="font-bold">$692.00</span>
                                    </div>
                                    <div class="h-px w-full bg-blue-700 my-4"></div>
                                    <div class="flex justify-between items-center pt-4 border-t border-blue-700">
                                        <span class="text-xl font-bold text-blue-100">Total Amount</span>
                                        <span class="text-3xl font-bold">$12,472.00</span>
                                    </div>
                                </div>

                                <div class="text-center pt-4 border-t border-blue-700">
                                    <p class="text-blue-200 text-sm mb-2">Payment Due By: <span class="font-bold">Dec
                                            1, 2023</span></p>
                                    <p class="text-blue-200 text-xs">A 30% deposit is required to confirm booking</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes Section -->
                <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> Travel
                        Instructions & Policies
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div
                            class="bg-gradient-to-br from-amber-50 to-white rounded-2xl p-6 border border-amber-100 shadow-sm">
                            <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-exclamation-circle text-amber-500 mr-3"></i> Important Notes
                            </h4>
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-passport text-blue-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Passport Validity:</span> Must be valid for at
                                        least 6 months beyond travel dates</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-suitcase text-green-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Check-in:</span> Hotel check-in at 3:00 PM,
                                        check-out at 11:00 AM</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-plane text-blue-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Flight Details:</span> Will be emailed 72 hours
                                        before departure</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-user-md text-red-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Travel Insurance:</span> Highly recommended (not
                                        included)</span>
                                </li>
                            </ul>
                        </div>

                        <div
                            class="bg-gradient-to-br from-red-50 to-white rounded-2xl p-6 border border-red-100 shadow-sm">
                            <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-file-contract text-red-500 mr-3"></i> Cancellation Policy
                            </h4>
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-calendar-times text-red-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">30+ days before:</span> Full refund (minus $50
                                        processing fee)</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-calendar-minus text-amber-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">15-29 days before:</span> 50% refund</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-calendar-day text-red-600 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Less than 15 days:</span> No refund</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-plane-slash text-gray-500 mr-3 mt-1"></i>
                                    <span><span class="font-semibold">Flight cancellation:</span> Subject to airline
                                        policies</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="mt-8 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-200">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-star text-yellow-500 mr-3"></i> Special Instructions for Your Trip
                        </h4>
                        <p class="text-gray-700 mb-3">Congratulations on booking your dream vacation! Here are some
                            personalized notes for your trip:</p>
                        <ul class="list-disc pl-5 text-gray-700 space-y-2">
                            <li>We've arranged a surprise anniversary cake for your 10th anniversary on Dec 20th in Bali
                            </li>
                            <li>Your villa in Bali has been upgraded to a premium ocean view at no additional cost</li>
                            <li>A child-friendly guide will be assigned for your family during sightseeing tours</li>
                            <li>Vegetarian meal preferences have been noted for all included meals</li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="p-6 md:p-8 lg:p-10 bg-gradient-to-r from-blue-900 to-blue-800 text-white">
                    <div class="text-center mb-10">
                        <h3 class="text-3xl font-bold mb-6">Thank you for choosing Wanderlust Adventures!</h3>
                        <p class="text-blue-200 text-lg max-w-3xl mx-auto">We're excited to help you create
                            unforgettable memories. Our team is dedicated to ensuring your journey is seamless and
                            extraordinary.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center mb-10">
                        <div class="bg-blue-800 bg-opacity-50 rounded-2xl p-6">
                            <h4 class="font-bold text-xl mb-4 flex items-center justify-center">
                                <i class="fas fa-headset mr-3 text-cyan-300"></i> 24/7 Support
                            </h4>
                            <p class="text-blue-200 mb-2">+1 (800) 555-HELP</p>
                            <p class="text-blue-200 text-sm">Emergency travel assistance available worldwide</p>
                        </div>

                        <div class="bg-blue-800 bg-opacity-50 rounded-2xl p-6">
                            <h4 class="font-bold text-xl mb-4 flex items-center justify-center">
                                <i class="fas fa-envelope-open-text mr-3 text-cyan-300"></i> Contact Us
                            </h4>
                            <p class="text-blue-200 mb-2">support@wanderlust.com</p>
                            <p class="text-blue-200 text-sm">Response within 2 hours during business hours</p>
                        </div>

                        <div class="bg-blue-800 bg-opacity-50 rounded-2xl p-6">
                            <h4 class="font-bold text-xl mb-4 flex items-center justify-center">
                                <i class="fas fa-globe-americas mr-3 text-cyan-300"></i> Visit Us
                            </h4>
                            <p class="text-blue-200 mb-2">www.wanderlustadventures.com</p>
                            <p class="text-blue-200 text-sm">Manage your booking online anytime</p>
                        </div>
                    </div>

                    <div class="border-t border-blue-700 pt-8 text-center">
                        <p class="text-blue-300">This is a computer-generated invoice. No physical signature is
                            required.</p>
                        <p class="text-blue-300 text-sm mt-2">Invoice ID: TRAV-2023-0876 | Generated: November 20, 2023
                        </p>
                        <p class="text-blue-300 text-sm">Wanderlust Adventures LLC | 123 Travel Street, Miami, FL
                            33132, USA</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Make the Pay Now button functional
            document.addEventListener('DOMContentLoaded', function() {
                const payButton = document.getElementById('payNowBtn');
                if (payButton) {
                    payButton.addEventListener('click', function() {
                        const modal = document.createElement('div');
                        modal.className =
                            'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 no-print';
                        modal.innerHTML = `
                        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Payment Portal</h3>
                            <p class="text-gray-700 mb-6">You will be redirected to our secure payment gateway to complete your booking.</p>
                            <div class="flex justify-end space-x-4">
                                <button id="cancelPayment" class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button id="confirmPayment" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700">Continue to Payment</button>
                            </div>
                        </div>
                    `;
                        document.body.appendChild(modal);

                        document.getElementById('cancelPayment').addEventListener('click', function() {
                            document.body.removeChild(modal);
                        });

                        document.getElementById('confirmPayment').addEventListener('click', function() {
                            alert('Thank you! You are being redirected to our secure payment gateway.');
                            document.body.removeChild(modal);
                            // In real application, redirect to payment page
                            // window.location.href = '/payment';
                        });
                    });
                }
            });
        </script>

    </div>
    </div>
    </div>
</x-app-layout>
