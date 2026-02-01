@extends('layouts.app')

@section('title', 'Pickup QR Code')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-8">
                <h1 class="text-2xl font-bold text-white text-center">
                    Pickup Verification QR Code
                </h1>
                <p class="text-green-100 text-center mt-2">
                    Show this to the recipient for pickup verification
                </p>
            </div>

            <!-- QR Code Section -->
            <div class="p-8">
                <div class="text-center">
                    @if($qrCodeImage)
                        <div class="mb-6">
                            <img src="{{ $qrCodeImage }}"
                                 alt="Pickup QR Code"
                                 class="mx-auto rounded-lg shadow-md border-2 border-gray-200"
                                 style="width: 300px; height: 300px;">
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="mx-auto bg-gray-100 rounded-lg shadow-md border-2 border-gray-200"
                                 style="width: 300px; height: 300px; display: flex; align-items: center; justify-content: center;">
                                <span class="text-gray-500">QR Code will appear here</span>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                            Code: {{ $verification->verification_code }}
                        </span>
                    </div>

                    <!-- Verification Details -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 mb-3">Pickup Details</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Food Item:</dt>
                                <dd class="text-gray-900 font-medium">
                                    {{ $verification->foodMatch->foodListing->food_name ?? $verification->foodListing->food_name ?? 'N/A' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Quantity:</dt>
                                <dd class="text-gray-900 font-medium">
                                    @if($verification->foodMatch->foodListing)
                                        {{ $verification->foodMatch->foodListing->quantity }} {{ $verification->foodMatch->foodListing->unit }}
                                    @elseif($verification->foodListing)
                                        {{ $verification->foodListing->quantity }} {{ $verification->foodListing->unit }}
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Recipient:</dt>
                                <dd class="text-gray-900 font-medium">
                                    {{ $verification->foodMatch->recipient->name ?? $verification->recipient->name ?? 'N/A' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Pickup Time:</dt>
                                <dd class="text-gray-900 font-medium">
                                    @if($verification->foodMatch->pickup_scheduled_at)
                                        {{ $verification->foodMatch->pickup_scheduled_at->format('F j, Y \a\t g:i A') }}
                                    @else
                                        Not scheduled yet
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Status:</dt>
                                <dd>
                                    @if($verification->verification_status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($verification->verification_status == 'verified')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $verification->verification_status }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                            <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                            Instructions
                        </h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Ask the recipient to scan this QR code using their phone</li>
                            <li>• Each QR code can only be used once</li>
                            <li>• After scanning, the recipient will complete the pickup</li>
                            <li>• You'll receive a notification once pickup is verified</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('restaurant.matches.show', $verification->food_match_id) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Back to Match
                        </a>
                        <button onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                            Print QR Code
                        </button>
                        @if($verification->verification_status == 'pending')
                            <button onclick="cancelVerification()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                Cancel Verification?
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this verification? The QR code will no longer be valid and you'll need to generate a new one.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="cancelConfirmBtn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700">
                    Yes, Cancel
                </button>
                <button onclick="closeCancelModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400">
                    No, Keep
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    function cancelVerification() {
        if (confirm('Are you sure you want to cancel this verification?')) {
            fetch(`/restaurant/pickup-verification/{{ $verification->id }}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification cancelled successfully');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling verification');
            });
        }
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Print functionality
    window.onload = function() {
        if (window.location.search.includes('print=true')) {
            window.print();
        }
    };
</script>
@endpush
@endsection