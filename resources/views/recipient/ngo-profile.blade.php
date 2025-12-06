@extends('recipient.layouts.recipient-layout')

@section('title', 'NGO Profile')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">NGO Profile</h1>
            <p class="text-sm text-zinc-500 mt-1">Manage your organization's information and settings.</p>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                Preview
            </button>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="flex-1 overflow-y-auto px-6 md:p-8 pb-6">
        <div class="space-y-6">
            <!-- Organization Header -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <div class="flex items-start gap-6">
                    <div class="w-20 h-20 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="building-2" class="w-10 h-10 text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-xl font-bold text-zinc-900">City Shelter</h2>
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-1 rounded-full">Verified</span>
                        </div>
                        <p class="text-sm text-zinc-600 mb-4">Providing shelter, meals, and support to homeless individuals and families since 2010.</p>
                        <div class="flex gap-4 text-sm">
                            <span class="text-zinc-500">Member since: January 2023</span>
                            <span class="text-zinc-500">Status: Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Organization Name</label>
                        <input type="text" value="City Shelter" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Organization Type</label>
                        <select class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Shelter</option>
                            <option>Food Bank</option>
                            <option>Community Kitchen</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Registration Number</label>
                        <input type="text" value="CS-2023-001" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Tax Exempt Status</label>
                        <select class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Yes</option>
                            <option>No</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Primary Email</label>
                        <input type="email" value="info@cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Alternative Email</label>
                        <input type="email" value="admin@cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Primary Phone</label>
                        <input type="tel" value="+60 12-345 6789" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Alternative Phone</label>
                        <input type="tel" value="+60 17-987 6543" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Address</label>
                        <input type="text" value="123 Jalan Shelter, Kuala Lumpur, Malaysia" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Website</label>
                        <input type="url" value="https://cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Organization Details -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Organization Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Mission Statement</label>
                        <textarea rows="3" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">To provide shelter, food, and support services to individuals and families experiencing homelessness, helping them rebuild their lives with dignity and hope.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Description</label>
                        <textarea rows="4" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">City Shelter has been serving the community since 2010, providing not just accommodation but comprehensive support services including meals, counseling, job placement assistance, and educational programs. We currently house up to 150 individuals nightly and serve over 500 meals daily.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Service Area</label>
                        <input type="text" value="Kuala Lumpur and surrounding areas" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Annual Capacity</label>
                            <input type="number" value="150" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Staff Members</label>
                            <input type="number" value="25" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Volunteers</label>
                            <input type="number" value="120" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Required Documents</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Registration Certificate</p>
                                <p class="text-xs text-zinc-500">city_shelter_cert.pdf</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-emerald-600 font-medium">Uploaded</span>
                            <button class="text-xs text-blue-600 hover:text-blue-700">Replace</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Tax Exempt Certificate</p>
                                <p class="text-xs text-zinc-500">tax_exempt_cert.pdf</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-emerald-600 font-medium">Uploaded</span>
                            <button class="text-xs text-blue-600 hover:text-blue-700">Replace</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Bank Statement</p>
                                <p class="text-xs text-zinc-500">Not uploaded</p>
                            </div>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-700">Upload</button>
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