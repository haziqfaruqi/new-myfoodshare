@extends('recipient.layouts.recipient-layout')

@section('title', 'Verify Pickup')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-md border-b border-emerald-100/50 sticky top-0 z-50">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('recipient.dashboard') }}" class="p-2 hover:bg-emerald-50 rounded-lg transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-emerald-600"></i>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Verify Pickup</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                        Order #{{ $verification->food_match_id }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Restaurant Info Card -->
            <div class="bg-white rounded-xl border border-emerald-200 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i data-lucide="store" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $verification->foodMatch->foodListing->restaurantProfile->restaurant_name ?? $verification->foodMatch->foodListing->creator->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $verification->foodMatch->foodListing->food_name ?? $verification->foodMatch->foodListing->food_type }}
                        </p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i data-lucide="package" class="w-3 h-3"></i>
                                {{ $verification->foodMatch->foodListing->quantity }} {{ $verification->foodMatch->foodListing->unit ?? 'units' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $verification->foodMatch->pickup_scheduled_at?->format('M j, Y g:i A') ?? 'Scheduled' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Verification Code Display -->
                <div class="mt-6 p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                    <div class="text-center">
                        <p class="text-sm font-medium text-emerald-900 mb-2">Verification Code</p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-2xl font-bold font-mono text-emerald-900 tracking-wider">
                                {{ $verification->verification_code }}
                            </span>
                            <button onclick="copyVerificationCode()" class="p-2 hover:bg-emerald-100 rounded-lg transition-colors" title="Copy code">
                                <i data-lucide="copy" class="w-4 h-4 text-emerald-600"></i>
                            </button>
                        </div>
                        <p class="text-xs text-emerald-700 mt-2">
                            Please show this code to the restaurant staff for verification
                        </p>
                    </div>
                </div>
            </div>

            <!-- Scan Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Scan Restaurant QR Code</h3>

                <div class="space-y-4">
                    <!-- Camera Input -->
                    <div class="bg-gray-900 rounded-xl p-8 text-center relative overflow-hidden group cursor-pointer hover:bg-gray-800 transition-colors"
                         onclick="document.getElementById('qr-scanner').click()">
                        <input type="file" id="qr-scanner" accept="image/*" capture="environment" class="hidden" onchange="handleQRScan(event)">

                        <i data-lucide="camera" class="w-12 h-12 text-gray-500 mx-auto mb-3"></i>
                        <p class="text-gray-300 text-sm">Tap to open camera</p>
                        <p class="text-gray-500 text-xs mt-1">or enter code manually below</p>

                        <!-- Camera overlay -->
                        <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center">
                            <span class="text-white text-sm font-bold border border-white px-4 py-2 rounded-full">Scan QR Code</span>
                        </div>
                    </div>

                    <!-- Manual Code Entry -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Restaurant Pickup Code</label>
                        <div class="relative">
                            <input type="text"
                                   id="pickup-code"
                                   name="pickup_code"
                                   placeholder="Enter restaurant's pickup code"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-lg font-mono tracking-wider"
                                   maxlength="12">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quality Rating Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rate Food Quality</h3>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">How would you rate the quality of the food you received?</p>

                    <div class="flex justify-center gap-2" id="quality-rating">
                        <button type="button"
                                class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-400 transition-colors flex items-center justify-center"
                                onclick="setRating(1)"
                                data-rating="1">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </button>
                        <button type="button"
                                class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-400 transition-colors flex items-center justify-center"
                                onclick="setRating(2)"
                                data-rating="2">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </button>
                        <button type="button"
                                class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-400 transition-colors flex items-center justify-center"
                                onclick="setRating(3)"
                                data-rating="3">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </button>
                        <button type="button"
                                class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-400 transition-colors flex items-center justify-center"
                                onclick="setRating(4)"
                                data-rating="4">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </button>
                        <button type="button"
                                class="w-12 h-12 rounded-full border-2 border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-400 transition-colors flex items-center justify-center"
                                onclick="setRating(5)"
                                data-rating="5">
                            <i data-lucide="star" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <div class="text-center">
                        <span id="rating-text" class="text-sm text-gray-500">Click to rate</span>
                    </div>
                </div>
            </div>

            <!-- Photo Evidence (Optional) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Photo Evidence (Optional)</h3>

                <div class="space-y-3">
                    <p class="text-sm text-gray-600">Upload a photo of the food you received</p>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-emerald-400 transition-colors cursor-pointer"
                         onclick="document.getElementById('photo-upload').click()">
                        <input type="file"
                               id="photo-upload"
                               accept="image/*"
                               capture="environment"
                               class="hidden"
                               onchange="handlePhotoUpload(event)">

                        <div id="photo-preview" class="hidden">
                            <img id="preview-image" class="max-w-full h-48 object-cover rounded-lg mx-auto">
                            <button type="button"
                                    onclick="removePhoto()"
                                    class="mt-3 text-sm text-red-600 hover:text-red-700">
                                <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                Remove photo
                            </button>
                        </div>

                        <div id="photo-placeholder">
                            <i data-lucide="image-plus" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                            <p class="text-sm text-gray-500">Click to upload photo</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 2MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Any issues or comments?</label>
                    <textarea name="notes"
                              rows="3"
                              placeholder="Any issues with quantity, quality, or packaging?..."
                              maxlength="500"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">
                            <span id="char-count">0</span>/500
                        </span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3">
                <button type="button"
                        onclick="window.history.back()"
                        class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        onclick="submitVerification()"
                        class="flex-1 py-3 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Confirm Pickup
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Submission -->
    <form id="verification-form" action="{{ route('recipient.pickup.verify') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="hidden" name="verification_code" value="{{ $verification->verification_code }}">
        <input type="hidden" name="pickup_code" id="pickup-code-hidden">
        <input type="hidden" name="quality_rating" id="quality-rating-hidden">
        <input type="hidden" name="notes" id="notes-hidden">
        <input type="hidden" name="photo" id="photo-hidden">
    </form>
</div>

<!-- Success Toast -->
<div id="success-toast" class="hidden fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span>Pickup verified successfully!</span>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentRating = 0;
    let selectedPhoto = null;

    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Setup character counter
        const notesTextarea = document.querySelector('textarea[name="notes"]');
        const charCount = document.getElementById('char-count');

        notesTextarea?.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    });

    function copyVerificationCode() {
        navigator.clipboard.writeText('{{ $verification->verification_code }}').then(() => {
            // Show temporary success message
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.innerHTML = '<i data-lucide="check" class="w-4 h-4 inline mr-1"></i> Code copied!';
            document.body.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.remove();
            }, 2000);
        });
    }

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('quality-rating-hidden').value = rating;

        // Update star visuals
        const stars = document.querySelectorAll('#quality-rating button');
        const ratingText = document.getElementById('rating-text');

        const ratingTexts = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };

        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('border-gray-200', 'text-gray-300');
                star.classList.add('border-yellow-400', 'text-yellow-400');
                star.querySelector('i').classList.add('fill-current');
            } else {
                star.classList.remove('border-yellow-400', 'text-yellow-400');
                star.classList.add('border-gray-200', 'text-gray-300');
                star.querySelector('i').classList.remove('fill-current');
            }
        });

        ratingText.textContent = ratingTexts[rating];
        ratingText.classList.add('text-emerald-600', 'font-medium');
    }

    function handleQRScan(event) {
        const file = event.target.files[0];
        if (file) {
            // In a real implementation, you would use a QR code scanning library
            // For demo purposes, we'll just focus on manual entry
            console.log('QR code image selected:', file.name);
            // You would implement QR code reading here
        }
    }

    function handlePhotoUpload(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Photo must be less than 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                selectedPhoto = file;
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('photo-preview').classList.remove('hidden');
                document.getElementById('photo-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function removePhoto() {
        selectedPhoto = null;
        document.getElementById('photo-upload').value = '';
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('photo-placeholder').classList.remove('hidden');
        document.getElementById('photo-hidden').value = '';
    }

    function submitVerification() {
        const pickupCode = document.getElementById('pickup-code').value.trim();

        if (!pickupCode) {
            alert('Please enter the restaurant pickup code');
            return;
        }

        if (pickupCode !== '{{ $verification->verification_code }}') {
            alert('Invalid pickup code. Please try again.');
            return;
        }

        // Set form values
        document.getElementById('pickup-code-hidden').value = pickupCode;
        document.getElementById('quality-rating-hidden').value = currentRating;
        document.getElementById('notes-hidden').value = document.querySelector('textarea[name="notes"]').value;
        document.getElementById('photo-hidden').value = selectedPhoto ? selectedPhoto.name : '';

        // Submit form
        document.getElementById('verification-form').submit();
    }

    // Auto-fill pickup code for demo purposes
    document.addEventListener('DOMContentLoaded', function() {
        // In a real implementation, the restaurant would provide their actual code
        // For demo, we'll auto-fill it
        setTimeout(() => {
            document.getElementById('pickup-code').value = '{{ $verification->verification_code }}';
        }, 1000);
    });
</script>
@endsection