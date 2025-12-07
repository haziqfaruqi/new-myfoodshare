@extends('admin.layouts.admin-layout')

@php
    use App\Models\User;
    use App\Models\FoodListing;
@endphp

@section('title', 'User Management - Admin Panel')

@section('content')

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full">
    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">User Management</h1>
        <p class="text-sm text-zinc-500">Manage user roles, permissions, and access levels</p>
    </div>

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ User::count() }}</h3>
            <p class="text-sm text-zinc-500">Total Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-amber-600"></i>
                </div>
                <span class="text-sm font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded">{{ User::where('status', 'active')->count() }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ User::where('status', 'active')->count() }}</h3>
            <p class="text-sm text-zinc-500">Active Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="user-x" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ User::where('status', 'inactive')->count() }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ User::where('status', 'inactive')->count() }}</h3>
            <p class="text-sm text-zinc-500">Inactive Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-rose-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-rose-600"></i>
                </div>
                <span class="text-sm font-medium text-rose-600 bg-rose-50 px-2 py-1 rounded">{{ User::where('status', 'pending')->count() }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ User::where('status', 'pending')->count() }}</h3>
            <p class="text-sm text-zinc-500">Pending Review</p>
        </div>
    </div>

    <!-- User Management Table -->
    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900">All Users</h2>
                <div class="flex items-center gap-3">
                    <select class="px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option>All Roles</option>
                        <option>admin</option>
                        <option>restaurant_owner</option>
                        <option>recipient</option>
                    </select>
                    <select class="px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option>All Status</option>
                        <option>active</option>
                        <option>inactive</option>
                        <option>pending</option>
                    </select>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                        Add User
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Member Since</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Last Active</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200">
                    @foreach(User::take(10) as $user)
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-zinc-900 font-semibold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-zinc-900">{{ $user->name }}</div>
                                    <div class="text-sm text-zinc-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Active
                                </span>
                            @elseif($user->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $user->created_at->format('M j, Y') }}</div>
                            <div class="text-sm text-zinc-500">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $user->last_login ? $user->last_login->format('M j, Y') : 'Never' }}</div>
                            <div class="text-sm text-zinc-500">{{ $user->last_login ? $user->last_login->diffForHumans() : 'No activity' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="openEditModal({{ $user->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button onclick="confirmDelete({{ $user->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                <button class="p-2 text-zinc-600 hover:bg-zinc-50 rounded-lg transition-colors" title="View Profile">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-zinc-200 bg-zinc-50">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">Showing 1-10 of {{ User::count() }} users</p>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1 text-sm text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Previous</button>
                    <button class="px-3 py-1 text-sm text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-zinc-100">
            <h3 class="text-lg font-semibold text-zinc-900">Edit User</h3>
        </div>
        <form class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Role</label>
                <select class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option>admin</option>
                    <option>restaurant_owner</option>
                    <option>recipient</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="flex gap-3 pt-4 border-t border-zinc-100">
                <button type="button" onclick="closeEditModal()" class="flex-1 py-2 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-zinc-100">
            <h3 class="text-lg font-semibold text-zinc-900">Delete User</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-zinc-600 mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-colors">
                    Cancel
                </button>
                <button type="button" class="flex-1 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Delete User
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(userId) {
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    function confirmDelete(userId) {
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush