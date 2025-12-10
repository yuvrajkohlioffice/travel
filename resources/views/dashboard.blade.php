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
                   

                    <a href="{{ route('leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        New Lead
                    </a>
                </div>
            </header>

            {{-- Top stats cards --}}
            
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <a href="{{ route('leads.index') }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Leads</p>
                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $leadCount }}</p>
                            <p class="text-xs text-gray-400 mt-1">All-time</p>
                        </div>
                        <div class="text-indigo-600 dark:text-indigo-300 text-2xl">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                </a>
<a href="{{ route('invoices.index') }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Invoices</p>
                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $invoiceCount }}</p>
                            <p class="text-xs text-gray-400 mt-1">All-time</p>
                        </div>
                        <div class="text-indigo-500 dark:text-indigo-300 text-2xl">
                            <i class="fa fa-file-invoice-dollar" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
</a>
<a href="{{ route('invoices.index') }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">₹{{ number_format($totalRevenue, 2) }}</p>
                            <p class="text-xs text-gray-400 mt-1">All-time</p>
                        </div>
                        <div class="text-green-600 dark:text-green-400 text-2xl">
                            <i class="fa fa-rupee-sign" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
</a>
<a href="{{ route('packages.index') }}" class="block">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Packages</p>
                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $packageCount }}</p>
                            <p class="text-xs text-gray-400 mt-1">Active packages</p>
                        </div>
                        <div class="text-purple-500 dark:text-purple-300 text-2xl">
                            <i class="fa fa-box" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
</a>
                <a href="{{ route('users.index') }}" class="block">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition cursor-pointer">
        <p class="text-xs text-gray-500 dark:text-gray-400">Users</p>
        <div class="flex items-center justify-between mt-2">
            <div>
                <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $userCount }}</p>
                <p class="text-xs text-gray-400 mt-1">Total users</p>
            </div>
            <div class="text-yellow-500 dark:text-yellow-300 text-2xl">
                <i class="fa fa-user-circle" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</a>

            </section>

            {{-- Small summary blocks: Today / Week --}}
            <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="rounded-xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Leads Today</p>
                    <p class="text-xl font-semibold text-indigo-600 mt-2">{{ $todayLeads }}</p>
                </div>
                <div class="rounded-xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Leads This Week</p>
                    <p class="text-xl font-semibold text-indigo-600 mt-2">{{ $weekLeads }}</p>
                </div>
                <div class="rounded-xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Invoices Today</p>
                    <p class="text-xl font-semibold text-green-600 mt-2">{{ $todayInvoices }}</p>
                </div>
                <div class="rounded-xl p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Invoices This Week</p>
                    <p class="text-xl font-semibold text-green-600 mt-2">{{ $weekInvoices }}</p>
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
                    <form method="GET" class="flex flex-col sm:flex-row gap-3 items-center" aria-label="chart filters">
                        <select name="month" class="w-1/4 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                            <option value="">Month</option>
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>

                        <select name="year" class="w-1/4 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                            <option value="">Year</option>
                            @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>

                        <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />
                        <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition" />

                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                            Apply
                        </button>
                    </form>
                </div>

                {{-- Chart canvas --}}
                <div class="mt-6">
                    <canvas id="leadsInvoicesChart" class="w-full" height="140" aria-label="Leads and invoices chart"></canvas>
                </div>
            </section>

            {{-- Lead status summary grid --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @foreach ($leadStatusCounts as $status => $count)
                    <article class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($status) }}</p>
                                <p class="text-2xl font-semibold mt-1">{{ $count }}</p>
                            </div>
                            <div class="text-2xl text-gray-400">
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Upcoming Followups</h3>

                    </div>

                    @if ($upcomingFollowups->count())
                        <div class="overflow-x-auto -mx-2">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/40">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Lead</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Assigned</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Next Followup</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Remark</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-transparent divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($upcomingFollowups as $f)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">
                                                {{ $f->lead->name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $f->user->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-300">
                                                {{ $f->next_followup_date }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $f->remark ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm">
                                                <div class="inline-flex items-center gap-2">
                                                    <button type="button" class="actionBtn inline-flex items-center gap-2 px-3 py-1 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs hover:shadow-sm transition" data-lead="{{ $f->lead->id }}">
                                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                                        Call
                                                    </button>

                                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-600 text-white text-xs hover:bg-indigo-700 transition" onclick="openQuickModal({{ $f->lead->id }}, '{{ addslashes($f->lead->name) }}')">
                                                        <i class="fa fa-comment" aria-hidden="true"></i>
                                                        Note
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming followups.</p>
                    @endif
                </div>

                {{-- User Performance --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">User Performance</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Leads Created</p>
                    </div>

                    <div class="overflow-y-auto" style="max-height:420px;">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">User</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Leads Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($createdLeadsByUser as $stat)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                            {{ $stat->createdBy->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-indigo-600 dark:text-indigo-300 text-right">
                                            {{ $stat->total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

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
    <div id="quickModal" role="dialog" aria-modal="true" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeQuickModal()"></div>
        <div class="relative max-w-xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 z-10">
            <div class="flex items-start justify-between">
                <h3 id="quickModalTitle" class="text-lg font-semibold">Add Note</h3>
                <button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" onclick="closeQuickModal()">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <form id="quickModalForm" class="mt-4">
                <input type="hidden" name="lead_id" id="modalLeadId" value="">
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-300">Note</label>
                    <textarea id="modalNote" name="note" rows="4" class="mt-2 w-full px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition text-sm" placeholder="Quick remark or next steps..."></textarea>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm hover:shadow-sm transition" onclick="closeQuickModal()">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition">
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Icons (FontAwesome) - ensure your layout includes the FontAwesome script or CSS --}}
    {{-- Chart & small JS behaviors --}}
    <script>
        // Make backend data available to the chart
        window.chartData = @json($last30Days);

        document.addEventListener('DOMContentLoaded', function () {
            // Chart rendering
            const ctx = document.getElementById('leadsInvoicesChart');
            if (ctx && window.chartData) {
                const rawData = window.chartData;
                const labels = rawData.map(i => {
                    const d = new Date(i.date);
                    return String(d.getDate()).padStart(2,'0');
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
                            datasets: [
                                {
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
                                            return new Intl.DateTimeFormat('en-US', { day:'2-digit', month:'short', year:'numeric' }).format(new Date(iso));
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true, grid: { color: 'rgba(203,213,225,0.5)' } }
                            }
                        }
                    });
                }
            }

            // Global search (quick client-side highlight/filter)
            const searchInput = document.getElementById('globalSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', function (e) {
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
                quickForm.addEventListener('submit', function (ev) {
                    ev.preventDefault();
                    const lead_id = document.getElementById('modalLeadId').value;
                    const note = document.getElementById('modalNote').value.trim();

                    // Simple client-side validation
                    if (!lead_id || !note) {
                        alert('Please enter a note.');
                        return;
                    }

                    // Here you can send via fetch to your API endpoint, e.g. '/leads/{id}/notes'
                    // For now we'll simulate success and close modal
                    console.log('Save note for', lead_id, note);

                    // Close and clear
                    closeQuickModal();
                    // Optionally show a small toast — using simple alert (substitute with nicer UI)
                    alert('Note saved.');
                });
            }

            // Quick actions (demo)
            document.querySelectorAll('.actionBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const leadId = btn.getAttribute('data-lead');
                    // open dialer or modal, but for now just log
                    console.log('Call action for', leadId);
                    alert('Initiating call (demo) for lead id: ' + leadId);
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
