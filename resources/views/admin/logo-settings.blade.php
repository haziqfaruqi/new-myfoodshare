@extends('admin.layouts.admin-layout')

@section('title', 'Logo Settings - Admin Panel')

@section('content')

<div class="flex-1 overflow-y-auto p-6 md:p-8 scroll-smooth">

    <div class="space-y-8 w-full">

    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Logo Settings</h1>
        <p class="text-sm text-zinc-500">Upload and manage your organization logo</p>
    </div>

    <!-- Logo Upload Card -->
    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 mb-6">Upload Logo</h2>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                <p class="text-sm text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-lg">
                <p class="text-sm text-rose-800">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Upload Form -->
            <div>
                <form action="{{ route('admin.settings.logo.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="logo" class="block text-sm font-medium text-zinc-700 mb-2">Logo File</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-zinc-300 border-dashed rounded-lg hover:border-zinc-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i data-lucide="upload" class="mx-auto h-12 w-12 text-zinc-400"></i>
                                <div class="flex text-sm text-zinc-600">
                                    <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                        <span>Upload a file</span>
                                        <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-zinc-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-emerald-600 text-white py-2 px-4 rounded-md hover:bg-emerald-700 transition-colors font-medium">
                            Upload Logo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Logo Preview -->
            <div>
                <h3 class="text-sm font-medium text-zinc-700 mb-4">Current Logo</h3>
                <div class="flex items-center justify-center h-64 bg-zinc-50 rounded-lg border-2 border-zinc-200">
                    @if($currentLogo)
                        <img src="{{ asset($currentLogo) }}" alt="Current Logo" class="max-h-60 max-w-full object-contain">
                    @else
                        <div class="text-center text-zinc-400">
                            <i data-lucide="image-off" class="w-16 h-16 mx-auto mb-2"></i>
                            <p class="text-sm">No logo uploaded</p>
                        </div>
                    @endif
                </div>

                @if($currentLogo)
                    <div class="mt-4 text-center">
                        <a href="{{ asset($currentLogo) }}" target="_blank" class="text-sm text-emerald-600 hover:text-emerald-700">View full size</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Logo Guidelines -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Logo Guidelines</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Use a high-quality image with transparent background (PNG format recommended)</li>
                <li>• Recommended dimensions: 200x80 pixels or similar aspect ratio</li>
                <li>• Maximum file size: 2MB</li>
                <li>• Supported formats: PNG, JPG, GIF, SVG</li>
                <li>• The logo will be displayed in the sidebar and login page</li>
            </ul>
        </div>
    </div>

    <!-- Back to Admin Link -->
    <div class="pt-4">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-zinc-600 hover:text-zinc-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Admin Dashboard
        </a>
    </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // File upload preview
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update the preview area
                const preview = document.querySelector('.border-dashed');
                preview.innerHTML = `
                    <div class="space-y-1 text-center">
                        <img src="${e.target.result}" alt="Preview" class="mx-auto h-12 w-12 object-cover rounded">
                        <div class="text-sm text-zinc-600">
                            ${file.name}
                        </div>
                        <p class="text-xs text-zinc-500">Click to change</p>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush