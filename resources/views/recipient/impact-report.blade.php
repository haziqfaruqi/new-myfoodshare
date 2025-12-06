@extends('recipient.layouts.recipient-layout')

@section('title', 'Impact Report')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex-shrink-0 flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Impact Report</h1>
            <p class="text-sm text-zinc-500 mt-1">Track your organization's environmental and social impact.</p>
        </div>
        <div class="flex gap-3">
            <select class="px-4 py-2 border border-zinc-200 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>Last 30 Days</option>
                <option>Last 90 Days</option>
                <option>Last 6 Months</option>
                <option>This Year</option>
            </select>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Impact Overview Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 px-6 md:p-8 pb-4">
        <!-- Environmental Impact -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="p-1.5 bg-emerald-500 rounded-lg">
                    <i data-lucide="leaf" class="w-4 h-4 text-white"></i>
                </div>
                <span class="text-xs font-medium text-emerald-700 bg-emerald-200 px-2 py-1 rounded-full">+12%</span>
            </div>
            <div class="space-y-1">
                <p class="text-xs text-emerald-700">CO₂ Saved</p>
                <p class="text-2xl font-bold text-emerald-900">847 kg</p>
                <p class="text-xs text-emerald-600">Equivalent to 4 trees</p>
            </div>
        </div>

        <!-- Food Recovered -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="p-1.5 bg-orange-500 rounded-lg">
                    <i data-lucide="utensils" class="w-4 h-4 text-white"></i>
                </div>
                <span class="text-xs font-medium text-orange-700 bg-orange-200 px-2 py-1 rounded-full">+8%</span>
            </div>
            <div class="space-y-1">
                <p class="text-xs text-orange-700">Meals Recovered</p>
                <p class="text-2xl font-bold text-orange-900">1,242</p>
                <p class="text-xs text-orange-600">This month</p>
            </div>
        </div>

        <!-- Money Saved -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="p-1.5 bg-blue-500 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-4 h-4 text-white"></i>
                </div>
                <span class="text-xs font-medium text-blue-700 bg-blue-200 px-2 py-1 rounded-full">+15%</span>
            </div>
            <div class="space-y-1">
                <p class="text-xs text-blue-700">Money Saved</p>
                <p class="text-2xl font-bold text-blue-900">RM 62,100</p>
                <p class="text-xs text-blue-600">Total value</p>
            </div>
        </div>

        <!-- Community Impact -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="p-1.5 bg-purple-500 rounded-lg">
                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                </div>
                <span class="text-xs font-medium text-purple-700 bg-purple-200 px-2 py-1 rounded-full">+23%</span>
            </div>
            <div class="space-y-1">
                <p class="text-xs text-purple-700">People Helped</p>
                <p class="text-2xl font-bold text-purple-900">892</p>
                <p class="text-xs text-purple-600">Lives impacted</p>
            </div>
        </div>
    </div>

    <!-- Charts and Details Grid -->
    <div class="flex-1 overflow-y-auto px-6 md:p-8 pb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column: Charts -->
        <div class="lg:col-span-2 space-y-4 overflow-y-auto">
            <!-- Monthly Recovery Chart -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-zinc-900">Monthly Recovery Trend</h3>
                    <select class="text-xs border border-zinc-200 rounded px-2 py-1">
                        <option>2023</option>
                        <option>2024</option>
                    </select>
                </div>
                <div class="h-64 flex items-center justify-center bg-zinc-50 rounded-lg">
                    <p class="text-sm text-zinc-500">Chart visualization would appear here</p>
                </div>
            </div>

            <!-- Food Categories Distribution -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-zinc-900 mb-4">Food Categories Distribution</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700">Italian</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-zinc-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: 35%"></div>
                            </div>
                            <span class="text-sm text-zinc-600 w-12">35%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700">Chinese</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-zinc-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: 28%"></div>
                            </div>
                            <span class="text-sm text-zinc-600 w-12">28%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700">Indian</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-zinc-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                            <span class="text-sm text-zinc-600 w-12">20%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700">Mexican</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-zinc-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 12%"></div>
                            </div>
                            <span class="text-sm text-zinc-600 w-12">12%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-700">American</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-zinc-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 5%"></div>
                            </div>
                            <span class="text-sm text-zinc-600 w-12">5%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environmental Impact Timeline -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-zinc-900 mb-4">Environmental Milestones</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900">First 100 kg CO₂ Saved</p>
                            <p class="text-xs text-zinc-500">Achieved on March 15, 2023</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900">1,000 Meals Milestone</p>
                            <p class="text-xs text-zinc-500">Achieved on June 22, 2023</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900">500 People Impacted</p>
                            <p class="text-xs text-zinc-500">Achieved on September 10, 2023</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats and Details -->
        <div class="space-y-4">
            <!-- Key Statistics -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-zinc-900 mb-4">Key Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600">Average Rating</span>
                        <div class="flex items-center gap-1">
                            <i data-lucide="star" class="w-4 h-4 text-amber-500"></i>
                            <span class="font-medium">4.8</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600">Success Rate</span>
                        <span class="font-medium text-emerald-600">94%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600">Avg. Response Time</span>
                        <span class="font-medium">2.3 hours</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-zinc-600">Partner Restaurants</span>
                        <span class="font-medium">24</span>
                    </div>
                </div>
            </div>

            <!-- Recent Success Stories -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-zinc-900 mb-4">Success Stories</h3>
                <div class="space-y-3">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-900 font-medium">Family Shelter Program</p>
                        <p class="text-xs text-blue-700 mt-1">Provided 500 meals to families in need</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-900 font-medium">Community Kitchen</p>
                        <p class="text-xs text-green-700 mt-1">Supported daily meal preparation</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <p class="text-sm text-emerald-900 font-medium">School Feeding Program</p>
                        <p class="text-xs text-emerald-700 mt-1">Nutritious meals for underprivileged children</p>
                    </div>
                </div>
            </div>

            <!-- Recognition -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-3">
                    <i data-lucide="trophy" class="w-6 h-6 text-amber-600"></i>
                    <h3 class="font-semibold text-amber-900">Recognition</h3>
                </div>
                <p class="text-sm text-amber-700 mb-2">Environmental Champion Award</p>
                <p class="text-xs text-amber-600">Received for outstanding contribution to food waste reduction</p>
            </div>
        </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection