@extends('admin.layouts.admin-layout')

@php
    use App\Models\User;
    use App\Models\FoodListing;
@endphp

@section('title', 'User Approvals - Admin Panel')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">
    <div class="p-6 md:p-8 scroll-smooth">
        <!-- Header -->
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">User Approvals</h1>
            <p class="text-sm text-zinc-500">Review and manage user registration requests</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-zinc-500 uppercase">Pending Review</span>
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $pendingUsers->count() }}</span>
                    <span class="text-xs font-medium text-zinc-500">Awaiting approval</span>
                </div>
            </div>

            <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-zinc-500 uppercase">Total Users</span>
                    <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ User::count() }}</span>
                    <span class="text-xs font-medium text-emerald-600">+{{ $recentlyApproved->count() }} today</span>
                </div>
            </div>

            <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-zinc-500 uppercase">Active Users</span>
                    <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ User::where('status', 'active')->count() }}</span>
                    <span class="text-xs font-medium text-zinc-500">{{ round(User::where('status', 'active')->count() / User::count() * 100) }}%</span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            <!-- Pending Users -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-zinc-900">Pending Users</h2>
                            <span class="text-sm text-zinc-500">{{ $pendingUsers->total() }} applications</span>
                        </div>
                    </div>

                    <div class="divide-y divide-zinc-100">
                        @forelse($pendingUsers as $user)
                        <div class="p-6 hover:bg-zinc-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-zinc-900 font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-medium text-zinc-900">{{ $user->name }}</h3>
                                            <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Pending</span>
                                        </div>
                                        <p class="text-xs text-zinc-500 mt-1">{{ $user->email }}</p>
                                        <p class="text-xs text-zinc-500 mt-1">Role: {{ ucfirst($user->role) }}</p>
                                        <p class="text-xs text-zinc-400 mt-1">Applied: {{ $user->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-md transition-colors flex items-center gap-1">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            Approve
                                        </button>
                                    </form>
                                    <button onclick="showRejectModal({{ $user->id }})" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-medium rounded-md transition-colors flex items-center gap-1">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center">
                            <i data-lucide="check-circle" class="w-12 h-12 text-emerald-500 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-zinc-900 mb-2">No Pending Users</h3>
                            <p class="text-sm text-zinc-500">All user applications have been reviewed and processed.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-zinc-500">Showing {{ $pendingUsers->firstItem() }}-{{ $pendingUsers->lastItem() }} of {{ $pendingUsers->total() }}</p>
                            <div class="flex items-center gap-2">
                                @if($pendingUsers->hasPages())
                                    @if($pendingUsers->onFirstPage())
                                        <button class="px-3 py-1 text-xs text-zinc-400 cursor-not-allowed">Previous</button>
                                    @else
                                        <a href="{{ $pendingUsers->previousPageUrl() }}" class="px-3 py-1 text-xs text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Previous</a>
                                    @endif

                                    <span class="px-3 py-1 text-xs text-zinc-900 bg-zinc-100 rounded-md">{{ $pendingUsers->currentPage() }} / {{ $pendingUsers->lastPage() }}</span>

                                    @if($pendingUsers->hasMorePages())
                                        <a href="{{ $pendingUsers->nextPageUrl() }}" class="px-3 py-1 text-xs text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Next</a>
                                    @else
                                        <button class="px-3 py-1 text-xs text-zinc-400 cursor-not-allowed">Next</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recently Approved -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white shadow-lg">
                    <h3 class="text-sm font-medium mb-3">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                            <i data-lucide="layout-grid" class="w-4 h-4"></i>
                            Dashboard Overview
                        </a>
                        <a href="{{ route('admin.food-listings') }}" class="flex items-center gap-2 text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            Listing Approvals
                        </a>
                    </div>
                </div>

                <!-- Recently Approved -->
                <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-medium text-zinc-900 mb-4">Recently Approved</h3>
                    <div class="space-y-3">
                        @forelse($recentlyApproved as $user)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-zinc-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-zinc-400">{{ $user->approved_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-zinc-500 text-center py-4">No recent approvals</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-12 mb-4 text-center">
    <p class="text-xs text-zinc-400">© 2024 MyFoodshare Platform. Reducing waste, feeding communities.</p>
</footer>

<!-- Reject User Modal -->
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('reject-modal').classList.add('hidden')"></div>
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-100">
            <h3 class="font-semibold text-zinc-900">Reject User Application</h3>
        </div>

        <form action="#" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 mb-2">Rejection Reason</label>
                    <textarea name="rejection_reason" rows="4" placeholder="Please explain why this user application is being rejected..."
                              class="w-full px-3 py-2 border border-zinc-200 rounded-md text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"
                              required></textarea>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-md p-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 mt-0.5"></i>
                        <div>
                            <p class="text-xs font-medium text-amber-800">Important</p>
                            <p class="text-xs text-amber-700 mt-0.5">The user will be notified of this rejection and will not be able to reapply.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                        class="flex-1 py-2 border border-zinc-200 text-zinc-700 rounded-md font-medium hover:bg-zinc-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-md font-medium hover:bg-red-500 transition-colors">
                    Reject User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRejectModal(userId) {
        document.getElementById('reject-modal').classList.remove('hidden');
        // Update form action
        const form = document.querySelector('#reject-modal form');
        form.setAttribute('action', `/admin/users/${userId}/reject`);
    }

    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush