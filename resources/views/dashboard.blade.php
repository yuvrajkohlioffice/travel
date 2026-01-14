<x-app-layout>
    {{-- Main wrapper: respects any existing global sidebar/layout the app provides --}}
    <div class="ml-64 min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Top bar / header --}}
            <header class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight">Dashboard Overview</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Summary of leads, invoices and user performance — last 30 days.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('leads.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition shadow-sm hover:shadow-md">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        New Lead
                    </a>
                </div>
            </header>

            {{-- Top stats cards --}}
            {{-- Dashboard Filters --}}
            <section class="bg-white dark:bg-gray-800 rounded-2xl p-4 mb-6 shadow-sm flex flex-wrap gap-4 items-center">
                <form method="GET" class="flex flex-wrap gap-3 items-center w-full" aria-label="dashboard filters">

                    {{-- Month --}}
                    <select name="month"
                        class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                        <option value="">Month</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Year --}}
                    <select name="year"
                        class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                        <option value="">Year</option>
                        @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endforeach
                    </select>

                    {{-- Date range --}}
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />

                    {{-- Status filter --}}
                    <select name="status"
                        class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                        <option value="">All Statuses</option>
                        @foreach ($leadStatusCounts as $status => $count)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                        Apply
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm hover:shadow-sm transition">
                        Reset
                    </a>
                </form>
            </section>


            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <x-dashboard-card title="Total Leads" value="{{ $leadCount }}" icon="fa fa-users"
                    color="text-indigo-600 dark:text-indigo-300" link="{{ route('leads.index') }}"
                    subtitle="All-time" />
                <x-dashboard-card title="Total Invoices" value="{{ $invoiceCount }}" icon="fa fa-file-invoice-dollar"
                    color="text-green-500 dark:text-green-300" link="{{ route('invoices.index') }}"
                    subtitle="All-time" />
                <x-dashboard-card title="Total Revenue" value="₹{{ number_format($totalRevenue, 2) }}"
                    icon="fa fa-rupee-sign" link="{{ route('payments.index') }}"
                    color="text-green-600 dark:text-green-400" subtitle="All-time" />
                <x-dashboard-card title="Packages" value="{{ $packageCount }}" icon="fa fa-box"
                    color="text-purple-500 dark:text-purple-300" link="{{ route('packages.index') }}"
                    subtitle="Active packages" />
                <x-dashboard-card title="Users" value="{{ $userCount }}" icon="fa fa-user-circle"
                    color="text-yellow-500 dark:text-yellow-300" link="{{ route('users.index') }}"
                    subtitle="Total users" />
            </section>


            {{-- Small summary blocks: Today / Week --}}
            <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="rounded-2xl p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Leads Today</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $todayLeads }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <i class="fa fa-user-plus text-lg"></i>
                    </div>
                </div>
                <div class="rounded-2xl p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Leads This Week</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $weekLeads }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <i class="fa fa-calendar-week text-lg"></i>
                    </div>
                </div>
                <div class="rounded-2xl p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoices Today</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $todayInvoices }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <i class="fa fa-file-invoice text-lg"></i>
                    </div>
                </div>
                <div class="rounded-2xl p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoices This Week</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $weekInvoices }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <i class="fa fa-file-invoice-dollar text-lg"></i>
                    </div>
                </div>
            </section>

            {{-- Chart + Filters card --}}
            <section class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">Last 30 Days Activity</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leads vs Invoices</p>
                    </div>

                    {{-- Filter form (GET) --}}
                    <form method="GET" class="flex flex-col sm:flex-row gap-3 items-center"
                        aria-label="chart filters">
                        <select name="month"
                            class="w-1/4 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                            <option value="">Month</option>
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>

                        <select name="year"
                            class="w-1/4 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                            <option value="">Year</option>
                            @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>

                        <input type="date" name="from" value="{{ request('from') }}"
                            class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                            Apply
                        </button>
                    </form>
                </div>

                {{-- Chart canvas --}}
                <div class="mt-6">
                    <canvas id="leadsInvoicesChart" class="w-full" height="140"
                        aria-label="Leads and invoices chart"></canvas>
                </div>
            </section>

            {{-- Lead status summary grid --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @foreach ($leadStatusCounts as $status => $count)
                    <article
                        class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ ucfirst($status) ?? 'N/A' }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $count }}</p>
                            </div>
                            <div class="text-2xl text-gray-300 dark:text-gray-600">
                                <i class="fa fa-flag" aria-hidden="true"></i>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            {{-- Two-column area: Upcoming followups + User performance --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

                {{-- Upcoming Followups --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                            <i class="fa fa-phone-alt text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upcoming Followups</h3>
                    </div>

                    <div class="overflow-x-auto -mx-2">
                        <table id="followupsTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th>Lead</th>
                                    <th>Assigned</th>
                                    <th>Next Followup</th>
                                    <th>Remark</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>

                {{-- User Performance --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg text-yellow-600 dark:text-yellow-400">
                                <i class="fa fa-trophy text-lg"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">User Performance</h3>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Leads Created</span>
                    </div>

                    <div class="overflow-x-auto mb-8">
                        <table id="usersTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th>User</th>
                                    <th>Leads Created</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                            <i class="fa fa-plane-departure text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Departures</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Client</th>
                                    <th class="px-4 py-3 font-medium">Package</th>
                                    <th class="px-4 py-3 font-medium">Phone</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($todayDepartures as $trip)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $trip->primary_full_name }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $trip->package->package_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $trip->lead->phone_number }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No departures today
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                            <i class="fa fa-plane-arrival text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Today's Returns</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Client</th>
                                    <th class="px-4 py-3 font-medium">Phone</th>
                                    <th class="px-4 py-3 font-medium">Started On</th>
                                    <th class="px-4 py-3 font-medium">Duration</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($todayReturns as $trip)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $trip->primary_full_name }}</td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $trip->lead->phone_number }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $trip->travel_start_date }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            {{ $trip->package->package_days ?? 0 }}D /
                                            {{ $trip->package->package_nights ?? 0 }}N
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No returns today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>




            {{-- Footer note / small actions --}}
            <footer class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <div>Showing data for <strong>last 30 days</strong>. Updated in real-time.</div>
                <div class="flex items-center gap-3">

                    <button id="refreshBtn"
                        class="px-3 py-1 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs hover:shadow-sm transition"
                        onclick="window.location.href='{{ route('dashboard') }}'">
                        <i class="fa fa-sync-alt" aria-hidden="true"></i> Refresh
                    </button>

                </div>
            </footer>
        </div>
    </div>

    {{-- Quick Modal (vanilla JS) --}}
    <div id="quickModal" role="dialog" aria-modal="true"
        class="fixed inset-0 z-50 hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeQuickModal()"></div>
        <div class="relative max-w-xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 z-10">
            <div class="flex items-start justify-between">
                <h3 id="quickModalTitle" class="text-lg font-semibold">Add Note</h3>
                <button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-200"
                    onclick="closeQuickModal()">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <form id="quickModalForm" class="mt-4">
                <input type="hidden" name="lead_id" id="modalLeadId" value="">
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Note</label>
                    <textarea id="modalNote" name="note" rows="4"
                        class="mt-2 w-full px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition text-sm"
                        placeholder="Quick remark or next steps..."></textarea>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm hover:shadow-sm transition"
                        onclick="closeQuickModal()">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition">
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Icons (FontAwesome) - ensure your layout includes the FontAwesome script or CSS --}}
    {{-- Chart & small JS behaviors --}}
    <script>
        $(document).ready(function() {

            // --------------------------
            // Followups DataTable
            // --------------------------
            $('#followupsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dashboard') }}',
                    data: {
                        datatable: 'followups',
                        month: '{{ request('month') }}',
                        year: '{{ request('year') }}',
                        from: '{{ request('from') }}',
                        to: '{{ request('to') }}'
                    }
                },
                columns: [{
                        data: 'lead_name',
                        name: 'lead_name'
                    },
                    {
                        data: 'assigned',
                        name: 'assigned'
                    },
                    {
                        data: 'next_followup',
                        name: 'next_followup'
                    },
                    {
                        data: 'remark',
                        name: 'remark'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // --------------------------
            // Users DataTable
            // --------------------------
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('dashboard') }}',
                    data: {
                        datatable: 'users'
                    }
                },
                columns: [{
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'leads_created',
                        name: 'leads_created',
                        className: 'text-right'
                    }
                ]
            });

        });
    </script>
    <script>
        // Make backend data available to the chart
        window.chartData = @json($last30Days);

        document.addEventListener('DOMContentLoaded', function() {
            // Chart rendering
            const ctx = document.getElementById('leadsInvoicesChart');
            if (ctx && window.chartData) {
                const rawData = window.chartData;
                const labels = rawData.map(i => {
                    const d = new Date(i.date);
                    return String(d.getDate()).padStart(2, '0');
                });

                // Build datasets
                const leads = rawData.map(i => i.leads ?? 0);
                const invoices = rawData.map(i => i.invoices ?? 0);

                // lazy load Chart.js if available on page
                if (typeof Chart !== 'undefined') {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'Leads',
                                    data: leads,
                                    borderColor: '#2563eb',
                                    borderWidth: 2,
                                    tension: 0.35,
                                    fill: false,
                                    pointRadius: 2,
                                },
                                {
                                    label: 'Invoices',
                                    data: invoices,
                                    borderColor: '#16a34a',
                                    borderWidth: 2,
                                    tension: 0.35,
                                    fill: false,
                                    pointRadius: 2,
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: (items) => {
                                            const index = items[0].dataIndex;
                                            const iso = rawData[index].date;
                                            return new Intl.DateTimeFormat('en-US', {
                                                day: '2-digit',
                                                month: 'short',
                                                year: 'numeric'
                                            }).format(new Date(iso));
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(203,213,225,0.5)'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Global search (quick client-side highlight/filter)
            const searchInput = document.getElementById('globalSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    // minimal: if user hits enter, submit to server search (or you can extend)
                    if (e.key === 'Enter') {
                        const q = e.target.value.trim();
                        if (!q) return;
                        const url = new URL(window.location.href);
                        url.searchParams.set('q', q);
                        window.location.href = url.toString();
                    }
                });
            }

            // Filters panel toggle
            const openFilters = document.getElementById('openFilters');
            if (openFilters) {
                openFilters.addEventListener('click', () => {
                    // toggle simple focus to month select
                    const sel = document.querySelector('select[name="month"]');
                    if (sel) sel.focus();
                });
            }

            // Modal form submission (example AJAX fallback)
            const quickForm = document.getElementById('quickModalForm');
            if (quickForm) {
                quickForm.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const lead_id = document.getElementById('modalLeadId').value;
                    const note = document.getElementById('modalNote').value.trim();

                    // Simple client-side validation
                    if (!lead_id || !note) {

                        toast('Please enter a note.', 'error');
                        return;
                    }

                    // Here you can send via fetch to your API endpoint, e.g. '/leads/{id}/notes'
                    // For now we'll simulate success and close modal
                    console.log('Save note for', lead_id, note);

                    // Close and clear
                    closeQuickModal();
                    // Optionally show a small toast — using simple alert (substitute with nicer UI)
                    toast('Note saved.');
                });
            }

            // Quick actions (demo)
            document.querySelectorAll('.actionBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const leadId = btn.getAttribute('data-lead');
                    // open dialer or modal, but for now just log
                    console.log('Call action for', leadId);
                    toast('Initiating call (demo) for lead id: ' + leadId);
                });
            });

            // Export CSV stub
            const exportCsv = document.getElementById('exportCsv');
            if (exportCsv) {
                exportCsv.addEventListener('click', () => {
                    // redirect to backend export route if provided
                    const url = new URL(window.location.href);
                    url.searchParams.set('export', 'csv');
                    window.location.href = url.toString();
                });
            }
        });

        // Modal helpers
        function openQuickModal(leadId, leadName) {
            const modal = document.getElementById('quickModal');
            if (!modal) return;
            document.getElementById('modalLeadId').value = leadId;
            document.getElementById('quickModalTitle').textContent = `Quick Note — ${leadName}`;
            document.getElementById('modalNote').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // trap focus (minimal)
            document.getElementById('modalNote').focus();
        }

        function closeQuickModal() {
            const modal = document.getElementById('quickModal');
            if (!modal) return;
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</x-app-layout>
