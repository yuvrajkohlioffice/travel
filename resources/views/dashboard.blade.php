<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Heading -->
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                    Dashboard Overview
                </h2>

                <!-- Top Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">Total Leads</p>
                        <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $leadCount }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">Total Invoices</p>
                        <h3 class="text-3xl font-bold text-indigo-600 mt-2">{{ $invoiceCount }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($totalRevenue, 2) }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">Packages</p>
                        <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ $packageCount }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-5">
                        <p class="text-gray-500 text-sm">Users</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ $userCount }}</h3>
                    </div>

                </div>


                <!-- TODAY / WEEK SUMMARY -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">

                    <div
                        class="bg-blue-50 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Leads Today</p>
                        <h3 class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2">{{ $todayLeads }}</h3>
                    </div>

                    <div
                        class="bg-blue-50 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Leads This Week</p>
                        <h3 class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2">{{ $weekLeads }}</h3>
                    </div>

                    <div
                        class="bg-green-50 dark:bg-green-900/40 border border-green-200 dark:border-green-700 rounded-xl p-5">
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Invoices Today</p>
                        <h3 class="text-3xl font-bold text-green-700 dark:text-green-300 mt-2">{{ $todayInvoices }}</h3>
                    </div>

                    <div
                        class="bg-green-50 dark:bg-green-900/40 border border-green-200 dark:border-green-700 rounded-xl p-5">
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Invoices This Week</p>
                        <h3 class="text-3xl font-bold text-green-700 dark:text-green-300 mt-2">{{ $weekInvoices }}</h3>
                    </div>
                </div>


                <!-- GRAPH -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mt-10">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">
                        Last 30 Days Activity
                    </h3>
                    <form method="GET" class="mb-6">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            <!-- Month -->
                            <select name="month" class="p-2 border rounded">
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Year -->
                            <select name="year" class="p-2 border rounded">
                                <option value="">Select Year</option>
                                @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- From Date -->
                            <input type="date" name="from" value="{{ request('from') }}"
                                class="p-2 border rounded">

                            <!-- To Date -->
                            <input type="date" name="to" value="{{ request('to') }}"
                                class="p-2 border rounded">
                        </div>

                        <button class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded">
                            Filter
                        </button>

                    </form>

                    <canvas id="leadsInvoicesChart" height="120"></canvas>
                </div>


                <!-- Lead Status Summary -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mt-10">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Lead Status Summary</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($leadStatusCounts as $status => $count)
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($status) }}</p>
                                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-200">{{ $count }}
                                </h3>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- UPCOMING FOLLOWUPS -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mt-10">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Upcoming Followups</h3>

                    @if ($upcomingFollowups->count())
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-600 dark:text-gray-300 border-b">
                                    <th class="py-2">Lead</th>
                                    <th class="py-2">Assigned To</th>
                                    <th class="py-2">Next Followup</th>
                                    <th class="py-2">Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingFollowups as $f)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $f->lead->name }}
                                        </td>
                                        <td class="py-2 text-gray-600 dark:text-gray-300">
                                            {{ $f->user->name }}
                                        </td>
                                        <td class="py-2 text-blue-600 dark:text-blue-300">
                                            {{ $f->next_followup_date }}
                                        </td>
                                        <td class="py-2 text-gray-600 dark:text-gray-300">
                                            {{ $f->remark ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No upcoming followups.</p>
                    @endif
                </div>


                <!-- USER PERFORMANCE -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 mt-10 mb-10">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">User Performance</h3>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-600 dark:text-gray-300 border-b">
                                <th class="py-2">User</th>
                                <th class="py-2">Leads Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($createdLeadsByUser as $stat)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-2 text-gray-800 dark:text-gray-200">
                                        {{ $stat->createdBy->name ?? 'Unknown' }}
                                    </td>
                                    <td class="py-2 font-semibold text-indigo-600 dark:text-indigo-300">
                                        {{ $stat->total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <script>
            window.chartData = @json($last30Days);
        </script>




        <script>
            document.addEventListener("DOMContentLoaded", () => {

                const ctx = document.getElementById("leadsInvoicesChart");
                if (!ctx) return;

                const rawData = window.chartData ?? [];

                // ------------------------------------------
                // 📌 1. Prepare Date Formats
                // ------------------------------------------

                // For chart (show only day)
                const chartLabels = rawData.map(item => {
                    const d = new Date(item.date);
                    return String(d.getDate()).padStart(2, "0"); // → "06"
                });

                // For dropdown (show like → 06 Dec 2025)
                const readableLabels = rawData.map(item => {
                    const d = new Date(item.date);

                    return new Intl.DateTimeFormat("en-US", {
                        day: "2-digit",
                        month: "short",
                        year: "numeric",
                    }).format(d); // → "06 Dec 2025"
                });

                // Keep ISO dates for backend filtering
                const isoDates = rawData.map(i => i.date);

                // OPTIONAL: attach to window for reuse in dropdowns
                window.chartFormattedDates = {
                    chartLabels,
                    readableLabels,
                    isoDates
                };

                // ------------------------------------------
                // 📌 2. Render Chart (only day on x-axis)
                // ------------------------------------------
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                                label: "Leads",
                                data: rawData.map(i => i.leads),
                                borderColor: "#2563eb",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: "Invoices",
                                data: rawData.map(i => i.invoices),
                                borderColor: "#16a34a",
                                borderWidth: 2,
                                tension: 0.4,
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    title: (tooltipItems) => {
                                        const index = tooltipItems[0].dataIndex;
                                        const isoDate = rawData[index].date;

                                        return new Intl.DateTimeFormat("en-US", {
                                            day: "2-digit",
                                            month: "short",
                                            year: "numeric",
                                        }).format(new Date(isoDate));
                                    }
                                }
                            }
                        }
                    }
                });


            });
        </script>


    </div>
</x-app-layout>
