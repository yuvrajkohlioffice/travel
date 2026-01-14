<x-guest-layout>

    


    <nav class="fixed w-full z-50 top-0 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                       
                        <img src="{{ asset('favicon_io/android-chrome-512x512.png') }}" alt="TrekosCRM - LOGO" class="w-8 h-8">
                   
                    <span class="font-bold text-xl tracking-tight text-slate-800">Trekos<span class="text-brand-600">CRM</span></span>
                </div>

                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-slate-600 hover:text-brand-600 font-medium transition">Features</a>
                    <a href="#packages" class="text-slate-600 hover:text-brand-600 font-medium transition">Itineraries</a>
                    <a href="#portal" class="text-slate-600 hover:text-brand-600 font-medium transition">Client Portal</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-full font-medium transition shadow-lg shadow-green-500/30">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 rounded-full font-medium transition shadow-lg shadow-brand-500/30">Log in</a>
                        
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-100 rounded-full blur-3xl opacity-50 mix-blend-multiply filter"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100 rounded-full blur-3xl opacity-50 mix-blend-multiply filter"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center lg:text-left grid lg:grid-cols-2 gap-12 items-center">
            
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-sm font-semibold mb-6">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                    </span>
                    Now with WhatsApp Integration
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold tracking-tight text-slate-900 leading-[1.1] mb-6">
                    The Operating System for <span class="text-brand-600">Travel Agencies</span>
                </h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                    Streamline your travel business. Manage leads, build complex packages with hotels & cars, automate invoices, and track payments—all in one dashboard.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition shadow-lg shadow-brand-500/25">
                        Start Managing Leads
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        View Features
                    </a>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[600px] lg:max-w-none">
                <div class="relative rounded-2xl bg-slate-900/5 p-2 ring-1 ring-inset ring-slate-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200">
                        <div class="bg-slate-50 border-b px-4 py-3 flex items-center gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="mx-auto text-xs text-slate-400 font-mono">crm.trekos.in/dashboard</div>
                        </div>
                        <div class="p-6 grid grid-cols-3 gap-4">
                            <div class="col-span-1 space-y-3">
                                <div class="h-8 bg-slate-100 rounded w-full"></div>
                                <div class="h-4 bg-slate-50 rounded w-3/4"></div>
                                <div class="h-4 bg-slate-50 rounded w-5/6"></div>
                                <div class="h-4 bg-brand-50 rounded w-full border-l-4 border-brand-500"></div>
                                <div class="h-4 bg-slate-50 rounded w-2/3"></div>
                            </div>
                            <div class="col-span-2 space-y-4">
                                <div class="flex justify-between">
                                    <div class="h-8 bg-slate-100 rounded w-1/3"></div>
                                    <div class="h-8 bg-brand-600 rounded w-1/4"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="h-24 bg-blue-50 rounded-lg border border-blue-100 p-3">
                                        <div class="text-blue-600 font-bold">24 Leads</div>
                                        <div class="text-xs text-blue-400 mt-1">Pending Action</div>
                                    </div>
                                    <div class="h-24 bg-green-50 rounded-lg border border-green-100 p-3">
                                        <div class="text-green-600 font-bold">$12,450</div>
                                        <div class="text-xs text-green-400 mt-1">Revenue this week</div>
                                    </div>
                                </div>
                                <div class="h-32 bg-slate-50 rounded border border-slate-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-brand-600 font-semibold uppercase tracking-wide text-sm">Core Capabilities</h2>
                <h3 class="mt-2 text-3xl font-bold text-slate-900 sm:text-4xl">Everything you need to run your agency</h3>
                <p class="mt-4 text-slate-500">From the first inquiry to the final invoice, we've optimized every route of your business logic.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Lead Management</h4>
                    <p class="text-slate-600">Track inquiries, assign status (New, Contacted, Converted), and assign leads to staff members effortlessly.</p>
                </div>

                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                        <i data-lucide="map" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Dynamic Packages</h4>
                    <p class="text-slate-600">Create complex packages combining Hotels, Cars, and Pickup points. Manage inventory and difficulty types.</p>
                </div>

                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                        <i data-lucide="message-circle" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">WhatsApp Integration</h4>
                    <p class="text-slate-600">Send PDF itineraries, invoices, and follow-up messages directly to your client's WhatsApp.</p>
                </div>

                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <i data-lucide="banknote" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Invoices & Payments</h4>
                    <p class="text-slate-600">Generate professional invoices, track partial payments, and manage multiple payment methods.</p>
                </div>

                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 mb-4 group-hover:bg-orange-600 group-hover:text-white transition">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Smart Notifications</h4>
                    <p class="text-slate-600">Daily reminders for follow-ups and payments ensure no opportunity slips through the cracks.</p>
                </div>

                <div class="card p-6 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition border border-slate-100 group">
                    <div class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center text-slate-700 mb-4 group-hover:bg-slate-700 group-hover:text-white transition">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Roles & Permissions</h4>
                    <p class="text-slate-600">Granular control over what your staff can access with our robust Role-Route controller.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="portal" class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 opacity-20 transform translate-x-1/2 -translate-y-1/2">
            <svg width="404" height="404" fill="none" viewBox="0 0 404 404"><defs><pattern id="85737c0e-0916-41d7-917f-596dc7edfa27" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" class="text-slate-600" fill="currentColor" /></pattern></defs><rect width="404" height="404" fill="url(#85737c0e-0916-41d7-917f-596dc7edfa27)" /></svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6">A Dedicated Guest Portal</h2>
                <p class="text-slate-300 text-lg mb-8">
                    Impress your clients with a secure, branded portal. They can view their invoices, approve itineraries, and update their details without calling you.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-green-400 w-6 h-6"></i>
                        <span>Secure Login with Lead ID & Password</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-green-400 w-6 h-6"></i>
                        <span>View and Download Invoices</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-green-400 w-6 h-6"></i>
                        <span>Mobile Optimized Experience</span>
                    </li>
                </ul>
                <div class="mt-8">
                    <span class="text-sm text-slate-400 block mb-2">Secure routes handled by:</span>
                    <code class="bg-slate-800 px-3 py-1 rounded text-pink-400 text-sm font-mono">/portal/login/{lead_id}</code>
                </div>
            </div>
            <div class="relative">
                <div class="bg-white rounded-lg p-2 shadow-2xl transform rotate-3 hover:rotate-0 transition duration-500">
                    <div class="bg-slate-50 border rounded flex flex-col items-center justify-center p-8 text-slate-800 h-64">
                        <i data-lucide="lock" class="w-12 h-12 text-slate-300 mb-4"></i>
                        <div class="font-bold text-xl">Client Login Area</div>
                        <div class="text-sm text-slate-500 mt-2">Enter Lead ID to access Invoice</div>
                        <div class="mt-4 w-full max-w-xs h-10 bg-slate-200 rounded animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-brand-50 border-b border-brand-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-4xl font-bold text-brand-600">100%</div>
                    <div class="text-sm font-medium text-slate-600 uppercase tracking-wide mt-1">Lead Tracking</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-brand-600">24/7</div>
                    <div class="text-sm font-medium text-slate-600 uppercase tracking-wide mt-1">System Uptime</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-brand-600">Auto</div>
                    <div class="text-sm font-medium text-slate-600 uppercase tracking-wide mt-1">Reminders</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-brand-600">Secure</div>
                    <div class="text-sm font-medium text-slate-600 uppercase tracking-wide mt-1">Data Storage</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl mb-6">Ready to streamline your travel business?</h2>
            <p class="text-lg text-slate-600 mb-10">
                Join the agency management platform that handles everything from 
                <span class="font-semibold text-brand-600">Hotels</span> and 
                <span class="font-semibold text-brand-600">Cars</span> to 
                <span class="font-semibold text-brand-600">Invoices</span>.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="px-8 py-4 bg-brand-600 text-white rounded-lg font-bold text-lg shadow-lg hover:bg-brand-700 transition transform hover:-translate-y-1">
                    Access Dashboard
                </a>
            </div>
        </div>
    </section>

    <footer class="bg-slate-50 border-t border-slate-200 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                       
                            <img src="{{ asset('favicon_io/android-chrome-512x512.png') }}" alt="TrekosCRM - LOGO" class="w-6 h-6">
                        
                        <span class="font-bold text-lg text-slate-800">TrekosCRM</span>
                    </div>
                    <p class="text-slate-500 text-sm">
                        Simplifying travel management with automated workflows and integrated communications.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="#" class="hover:text-brand-600">Lead Management</a></li>
                        <li><a href="#" class="hover:text-brand-600">Package Builder</a></li>
                        <li><a href="#" class="hover:text-brand-600">Client Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="#" class="hover:text-brand-600">Documentation</a></li>
                        <li><a href="#" class="hover:text-brand-600">API Status</a></li>
                        <li><a href="#" class="hover:text-brand-600">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="#" class="hover:text-brand-600">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-brand-600">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} TrekosCRM. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</x-guest-layout>