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
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ $stats['total_users'] ?? User::count() }}</h3>
            <p class="text-sm text-zinc-500">Total Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded">{{ $stats['active_users'] ?? 0 }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ $stats['active_users'] ?? 0 }}</h3>
            <p class="text-sm text-zinc-500">Active Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i data-lucide="user-x" class="w-6 h-6 text-gray-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded">{{ $stats['inactive_users'] ?? 0 }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ $stats['inactive_users'] ?? 0 }}</h3>
            <p class="text-sm text-zinc-500">Inactive Users</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm font-medium text-yellow-600 bg-yellow-50 px-2 py-1 rounded">{{ $stats['pending_users'] ?? 0 }}</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ $stats['pending_users'] ?? 0 }}</h3>
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
                    {{-- <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                        Add User
                    </button> --}}
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
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Last Active</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200">
                    @foreach($users as $user)
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
                            @elseif($user->status === 'suspended')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Suspended
                                </span>
                            @elseif($user->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i data-lucide="help-circle" class="w-3 h-3 mr-1"></i>
                                    {{ ucfirst($user->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $user->created_at->format('M j, Y') }}</div>
                            <div class="text-sm text-zinc-500">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 relative" style="position: relative; z-index: 10;">
                                <button type="button" class="edit-btn p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit" style="position: relative; z-index: 10; cursor: pointer;"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-role="{{ $user->role }}"
                                    data-user-status="{{ $user->status }}"
                                    data-user-created="{{ $user->created_at->format('M j, Y') }}">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete({{ $user->id }})" style="position: relative; z-index: 10;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete" style="cursor: pointer;">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                <button type="button" class="view-btn p-2 text-zinc-600 hover:bg-zinc-50 rounded-lg transition-colors" title="View Profile" style="position: relative; z-index: 10; cursor: pointer;"
                                    data-user-id="{{ $user->id }}">
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
                <p class="text-sm text-zinc-500">
                    Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} users
                </p>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target === this) closeEditModal()">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-zinc-100">
            <h3 class="text-lg font-semibold text-zinc-900">Edit User Status</h3>
        </div>
        <form id="edit-form" action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-user-id" name="user_id" value="">

            <!-- User Info Display (Read-only) -->
            <div class="p-4 bg-zinc-50 rounded-lg space-y-3">
                <div class="flex items-center gap-3">
                    <div id="edit-user-avatar" class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-zinc-900 font-semibold">
                        U
                    </div>
                    <div>
                        <p id="edit-user-name" class="font-medium text-zinc-900">User Name</p>
                        <p id="edit-user-email" class="text-sm text-zinc-500">user@email.com</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <p class="text-xs text-zinc-500 uppercase">Role</p>
                        <p id="edit-user-role-display" class="font-medium text-zinc-900">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500 uppercase">Member Since</p>
                        <p id="edit-user-created" class="font-medium text-zinc-900">-</p>
                    </div>
                </div>
            </div>

            <!-- Editable Status Field -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Status</label>
                <select id="edit-status" name="status" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                    <option value="rejected">Rejected</option>
                </select>
                <input type="hidden" id="edit-role" name="role" value="">
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

<!-- User Profile Modal -->
<div id="profile-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target === this) closeProfileModal()">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-zinc-900">User Profile</h3>
            <button onclick="closeProfileModal()" class="text-zinc-400 hover:text-zinc-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="profile-content" class="p-6">
            <!-- Profile content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Store base URLs from Laravel - use placeholder to get the route pattern
    const updateUrl = '{{ route('admin.users.update', ['user' => '__ID__']) }}';
    const profileUrl = '{{ route('admin.users.profile', ['user' => '__ID__']) }}';

    console.log('User management script loaded');
    console.log('Update URL:', updateUrl);
    console.log('Profile URL:', profileUrl);

    // Use event delegation for edit buttons
    document.addEventListener('click', function(e) {
        console.log('Click detected on:', e.target);

        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            console.log('Edit button clicked!');
            e.preventDefault();
            e.stopPropagation();
            const userId = editBtn.dataset.userId;
            const userName = editBtn.dataset.userName;
            const userEmail = editBtn.dataset.userEmail;
            const role = editBtn.dataset.userRole;
            const status = editBtn.dataset.userStatus;
            const createdAt = editBtn.dataset.userCreated;

            console.log('User ID:', userId, 'Name:', userName, 'Role:', role, 'Status:', status);

            // Update modal with user info
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-role').value = role;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-form').action = updateUrl.replace('__ID__', userId);

            // Update display fields
            document.getElementById('edit-user-avatar').textContent = userName.charAt(0).toUpperCase();
            document.getElementById('edit-user-name').textContent = userName;
            document.getElementById('edit-user-email').textContent = userEmail;
            document.getElementById('edit-user-role-display').textContent = role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ');
            document.getElementById('edit-user-created').textContent = createdAt;

            document.getElementById('edit-modal').classList.remove('hidden');
            return;
        }

        const viewBtn = e.target.closest('.view-btn');
        if (viewBtn) {
            console.log('View button clicked!');
            e.preventDefault();
            e.stopPropagation();
            const userId = viewBtn.dataset.userId;
            const profileModal = document.getElementById('profile-modal');
            const profileContent = document.getElementById('profile-content');

            profileContent.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div></div>';
            profileModal.classList.remove('hidden');

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch(profileUrl.replace('__ID__', userId), {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => {
                    console.log('Response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Profile data:', data);
                    if (data.success) {
                        profileContent.innerHTML = `
                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-zinc-900 font-bold text-xl">
                                        ${data.user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-zinc-900">${data.user.name}</h4>
                                        <p class="text-sm text-zinc-500">${data.user.email}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 bg-zinc-50 rounded-lg">
                                        <p class="text-xs text-zinc-500 uppercase">Role</p>
                                        <p class="font-medium text-zinc-900">${data.user.role.charAt(0).toUpperCase() + data.user.role.slice(1).replace('_', ' ')}</p>
                                    </div>
                                    <div class="p-3 bg-zinc-50 rounded-lg">
                                        <p class="text-xs text-zinc-500 uppercase">Status</p>
                                        <p class="font-medium ${data.user.status === 'active' ? 'text-emerald-600' : 'text-zinc-900'}">${data.user.status.charAt(0).toUpperCase() + data.user.status.slice(1)}</p>
                                    </div>
                                    <div class="p-3 bg-zinc-50 rounded-lg">
                                        <p class="text-xs text-zinc-500 uppercase">Member Since</p>
                                        <p class="font-medium text-zinc-900">${data.user.created_at || '-'}</p>
                                    </div>
                                </div>
                                ${data.user.phone ? `
                                <div class="p-3 bg-zinc-50 rounded-lg">
                                    <p class="text-xs text-zinc-500 uppercase">Phone</p>
                                    <p class="font-medium text-zinc-900">${data.user.phone}</p>
                                </div>
                                ` : ''}
                                ${data.user.address ? `
                                <div class="p-3 bg-zinc-50 rounded-lg">
                                    <p class="text-xs text-zinc-500 uppercase">Address</p>
                                    <p class="font-medium text-zinc-900">${data.user.address}</p>
                                </div>
                                ` : ''}
                                ${data.user.restaurant_profile ? `
                                <div class="p-4 bg-emerald-50 rounded-lg">
                                    <p class="text-xs text-emerald-600 uppercase font-medium mb-2">Restaurant Profile</p>
                                    <p class="font-medium text-zinc-900">${data.user.restaurant_profile.restaurant_name}</p>
                                    <p class="text-sm text-zinc-600">${data.user.restaurant_profile.address || ''}</p>
                                    ${data.user.restaurant_profile.phone ? `<p class="text-sm text-zinc-600">${data.user.restaurant_profile.phone}</p>` : ''}
                                </div>
                                ` : ''}
                                ${data.user.recipient_profile ? `
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-blue-600 uppercase font-medium mb-2">Recipient Profile</p>
                                    <p class="font-medium text-zinc-900">${data.user.recipient_profile.organization_name}</p>
                                    <p class="text-sm text-zinc-600">${data.user.recipient_profile.address || ''}</p>
                                    ${data.user.recipient_profile.phone ? `<p class="text-sm text-zinc-600">${data.user.recipient_profile.phone}</p>` : ''}
                                </div>
                                ` : ''}
                            </div>
                        `;
                    } else {
                        profileContent.innerHTML = '<p class="text-red-600 text-center">Failed to load user profile: ' + (data.message || 'Unknown error') + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading profile:', error);
                    profileContent.innerHTML = '<p class="text-red-600 text-center">Failed to load user profile. Please try again.</p>';
                });
            return;
        }
    });

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    function closeProfileModal() {
        document.getElementById('profile-modal').classList.add('hidden');
    }

    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            return true;
        }
        return false;
    }
</script>
@endpush