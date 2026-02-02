@extends('recipient.layouts.recipient-layout')

@section('title', 'Rate Your Pickup - MyFoodShare')

@section('content')
<style>
    body {
        overflow: auto !important;
        height: auto !important;
    }
    main {
        overflow: visible !important;
        height: auto !important;
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-4">
                <i data-lucide="check-circle" class="w-10 h-10 text-green-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pickup Verified!</h1>
            <p class="text-gray-600">Thank you for helping reduce food waste. Please rate your experience.</p>
        </div>

        <!-- Feedback Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">How was your pickup experience?</h2>

            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center gap-3">
                    <i data-lucide="package" class="w-8 h-8 text-blue-600"></i>
                    <div>
                        <p class="font-medium text-gray-900">{{ $verification->foodMatch->foodListing->food_name }}</p>
                        <p class="text-sm text-gray-600">
                            From: {{ $verification->foodMatch->foodListing->restaurantProfile->restaurant_name ?? 'Restaurant' }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('recipient.feedback.submit', $verification->id) }}" class="space-y-6">
                @csrf

                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Quality Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2" id="starRating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none"
                                    data-rating="{{ $i }}"
                                    onclick="setRating({{ $i }})">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <p class="mt-2 text-sm text-gray-500" id="ratingText">Click to rate</p>
                    <input type="hidden" name="quality_rating" id="ratingInput" value="5" required>
                </div>

                <!-- Rating Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button"
                            class="rating-option p-3 border-2 border-gray-200 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-all text-left"
                            onclick="selectRatingOption(5, this)">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">😊</span>
                            <span class="font-medium text-gray-900">Excellent</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Great quality, as expected</p>
                    </button>

                    <button type="button"
                            class="rating-option p-3 border-2 border-gray-200 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-all text-left"
                            onclick="selectRatingOption(4, this)">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🙂</span>
                            <span class="font-medium text-gray-900">Good</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minor issues but satisfied</p>
                    </button>

                    <button type="button"
                            class="rating-option p-3 border-2 border-gray-200 rounded-lg hover:border-amber-500 hover:bg-amber-50 transition-all text-left"
                            onclick="selectRatingOption(3, this)">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">😐</span>
                            <span class="font-medium text-gray-900">Fair</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Some quality concerns</p>
                    </button>

                    <button type="button"
                            class="rating-option p-3 border-2 border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all text-left"
                            onclick="selectRatingOption(2, this)">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">😞</span>
                            <span class="font-medium text-gray-900">Poor</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Below expectations</p>
                    </button>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Feedback (Optional)
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Share your experience about the food quality, pickup process, or any suggestions..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Your feedback helps restaurants improve their donations.</p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-emerald-600 text-white py-3 px-6 rounded-md hover:bg-emerald-700 transition-colors font-medium flex items-center justify-center">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Submit Feedback
                    </button>
                    <a href="{{ route('recipient.dashboard') }}"
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Skip
                    </a>
                </div>
            </form>
        </div>

        <!-- Thank You Note -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Your feedback helps us improve food quality and reduce waste even more! 🌍</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    let currentRating = 5;

    function setRating(rating) {
        currentRating = rating;
        document.getElementById('ratingInput').value = rating;

        // Update star colors
        const stars = document.querySelectorAll('.star-btn');
        const ratingTexts = ['Poor', 'Fair', 'Okay', 'Good', 'Excellent'];

        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });

        document.getElementById('ratingText').textContent = ratingTexts[rating - 1];

        // Update option buttons
        document.querySelectorAll('.rating-option').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'border-amber-500', 'bg-amber-50', 'border-red-500', 'bg-red-50');
            btn.classList.add('border-gray-200');
        });
    }

    function selectRatingOption(rating, element) {
        setRating(rating);

        // Highlight selected option
        document.querySelectorAll('.rating-option').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'border-amber-500', 'bg-amber-50', 'border-red-500', 'bg-red-50');
            btn.classList.add('border-gray-200');
        });

        const colorClass = rating >= 4 ? 'emerald' : (rating === 3 ? 'amber' : 'red');
        element.classList.remove('border-gray-200');
        element.classList.add('border-' + colorClass + '-500', 'bg-' + colorClass + '-50');
    }

    // Initialize with 5 stars
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        setRating(5);
    });
</script>
@endsection
