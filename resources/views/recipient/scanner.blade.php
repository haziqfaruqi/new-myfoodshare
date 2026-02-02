@extends('recipient.layouts.recipient-layout')

@section('title', 'Scan QR Code for Pickup')

@section('content')
<style>
    /* Override layout overflow for this page to allow scrolling */
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 text-center">
                Scan QR Code for Pickup
            </h1>
            <p class="text-gray-600 text-center mt-2">
                Scan the restaurant's QR code to verify your pickup
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Scanner Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="camera" class="w-5 h-5 mr-2"></i>
                    QR Scanner
                </h2>

                <!-- Scanner Container -->
                <div class="relative">
                    <div id="reader" class="w-full h-80 bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading scanner...</p>
                        </div>
                    </div>

                    <!-- Scanner Controls -->
                    <div class="mt-4 flex flex-col gap-3">
                        <button id="startButton"
                                onclick="console.log('Button clicked!', typeof handleStartClick); handleStartClick()"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                            Start Scanning
                        </button>
                        <button id="stopButton"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors hidden flex items-center justify-center">
                            <i data-lucide="stop" class="w-4 h-4 mr-2"></i>
                            Stop Scanning
                        </button>
                        <button id="switchButton"
                                class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition-colors hidden flex items-center justify-center">
                            <i data-lucide="rotate-cw" class="w-4 h-4 mr-2"></i>
                            Switch Camera
                        </button>
                    </div>
                </div>

                <!-- Manual Entry -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Manual Entry</h3>
                    <form id="manualForm" class="space-y-3">
                        <div>
                            <label for="verificationCode" class="block text-sm font-medium text-gray-700 mb-1">
                                Verification Code
                            </label>
                            <input type="text"
                                   id="verificationCode"
                                   name="verification_code"
                                   placeholder="Enter VRF-XXXXXX code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>
                        <button type="submit"
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors flex items-center justify-center">
                            <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                            Verify Manually
                        </button>
                    </form>
                </div>
            </div>

            <!-- Information Section -->
            <div class="space-y-6">
                <!-- Current Pickup -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
                        Current Pickup
                    </h2>

                    @if($currentPickup)
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Food Item:</span>
                                <span class="font-medium">{{ $currentPickup->foodListing->food_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Quantity:</span>
                                <span class="font-medium">{{ $currentPickup->foodListing->quantity }} {{ $currentPickup->foodListing->unit }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Restaurant:</span>
                                <span class="font-medium">{{ $currentPickup->foodListing->restaurantProfile->restaurant_name ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pickup Time:</span>
                                <span class="font-medium">{{ $currentPickup->pickup_scheduled_at->format('F j, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Address:</span>
                                <span class="font-medium text-right">{{ $currentPickup->foodListing->pickup_address ?? 'Location not specified' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="package" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                            <p class="text-gray-500">No active pickups scheduled</p>
                            <a href="{{ route('recipient.available-food') }}"
                               class="mt-3 text-blue-600 hover:text-blue-800 inline-flex items-center">
                                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                                Find Available Food
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Debug Info -->
                <div id="debugInfo" class="bg-gray-100 rounded-lg p-4 mb-4 text-sm hidden">
                    <h4 class="font-semibold mb-2">Debug Information</h4>
                    <div id="debugContent"></div>
                </div>

                <!-- Scanner Instructions -->
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                        How to Scan
                    </h3>
                    <ol class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-900 rounded-full flex items-center justify-center text-xs font-medium mr-2">1</span>
                            <span>Point your camera at the restaurant's QR code</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-900 rounded-full flex items-center justify-center text-xs font-medium mr-2">2</span>
                            <span>Hold the camera steady until the code is recognized</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-5 h-5 bg-blue-200 text-blue-900 rounded-full flex items-center justify-center text-xs font-medium mr-2">3</span>
                            <span>Complete the pickup verification process</span>
                        </li>
                    </ol>
                </div>

                <!-- Recent Verifications -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i data-lucide="history" class="w-5 h-5 mr-2"></i>
                        Recent Verifications
                    </h3>
                    @if($recentVerifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentVerifications as $verification)
                                <div class="border-l-4 @if($verification->verification_status == 'verified') border-green-500 @else border-yellow-500 @endif pl-3 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-sm">{{ $verification->foodMatch->foodListing->food_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $verification->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($verification->verification_status == 'verified')
                                                bg-green-100 text-green-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ $verification->verification_status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No recent verifications</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                Pickup Verified!
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Your pickup has been successfully verified. Thank you for helping reduce food waste!
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="window.location.href='{{ route("recipient.dashboard") }}'"
                        class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full hover:bg-blue-700">
                    Go to Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                Verification Failed
            </h3>
            <div class="mt-2 px-7 py-3">
                <p id="errorMessage" class="text-sm text-gray-500">
                    There was an error with the verification. Please try again.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeErrorBtn"
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full hover:bg-gray-400">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Processing verification...</p>
    </div>
</div>

@endsection

@section('scripts')
<!-- External library scripts -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Main script -->
<script>
console.log('Scanner script started');

    // Function to check if libraries are loaded
    function checkLibrariesLoaded() {
        return typeof Html5Qrcode !== 'undefined' && typeof lucide !== 'undefined';
    }

    // Wait for libraries to load
    function waitForLibraries(callback, maxAttempts = 20) {
        let attempts = 0;

        const checkInterval = setInterval(() => {
            attempts++;
            console.log(`Checking libraries... Attempt ${attempts}`);

            if (checkLibrariesLoaded()) {
                console.log('All libraries loaded!');
                clearInterval(checkInterval);
                callback();
            } else if (attempts >= maxAttempts) {
                console.error('Libraries failed to load after maximum attempts');
                clearInterval(checkInterval);
                document.getElementById('reader').innerHTML = '<div class="text-center"><div class="text-red-500 mb-4">⚠️</div><p class="text-red-600 text-sm">QR Code library failed to load. Please check your internet connection and refresh.</p></div>';
            }
        }, 500);
    }

    // Global variables
    let html5QrcodeScanner = null;
    let currentCameraId = null;

  // Initialize when ready
    waitForLibraries(() => {
        console.log('Initializing scanner...');

        // Initialize Lucide icons
        lucide.createIcons();

        // Initialize scanner after page load
        window.addEventListener('DOMContentLoaded', function() {
            console.log('DOM content loaded');

            // Check for camera support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                console.error('Camera not supported on this device');
                document.getElementById('reader').innerHTML = '<div class="text-center"><div class="text-red-500 mb-4">📷</div><p class="text-red-600 text-sm">Camera not supported on this device. Please use a modern browser.</p></div>';
            }

            // Show debug info
            showDebugInfo();
        });
    });

    // Fallback click handler - must be defined early
    function handleStartClick() {
        console.log('handleStartClick called!');
        if (typeof startScanning === 'function') {
            console.log('Calling startScanning...');
            startScanning();
        } else {
            console.error('startScanning not found:', typeof startScanning);
        }
    }

    function showDebugInfo() {
        const debugContent = document.getElementById('debugContent');
        debugContent.innerHTML = `
            <p><strong>Browser:</strong> ${navigator.userAgent}</p>
            <p><strong>Location:</strong> ${window.location.origin}</p>
            <p><strong>Html5Qrcode loaded:</strong> ${typeof Html5Qrcode !== 'undefined' ? 'Yes' : 'No'}</p>
            <p><strong>Camera API:</strong> ${navigator.mediaDevices ? 'Available' : 'Not available'}</p>
        `;

        const debugInfo = document.getElementById('debugInfo');
        debugInfo.classList.remove('hidden');
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log('QR Code scanned successfully:', decodedText);

        // Stop scanning immediately after success
        if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
            html5QrcodeScanner.stop().then(() => {
                console.log("QR Code scan stopped successfully");
            }).catch(err => {
                console.error("Unable to stop scanning.", err);
            });
        }

        // Extract verification code from the URL
        const code = extractVerificationCode(decodedText);
        if (code) {
            verifyPickup(code);
        } else {
            console.error('Invalid QR code format:', decodedText);
            showError('Invalid QR code format. Please scan a valid pickup verification code.');
        }
    }

    function onScanFailure(error) {
        // Handle scan failure, ignore continuous warnings
        console.warn(`QR Code scan error = ${error}`);
    }

    function extractVerificationCode(url) {
        // Handle different URL formats

        // Direct code format: VRF-XXXXXX
        if (url.startsWith('VRF-')) {
            return url;
        }

        // Extract from URL path like: http://localhost/pickup/VRF-XXXXXX
        const pathMatch = url.match(/\/pickup\/(VRF-[A-Z0-9-]+)/i);
        if (pathMatch) {
            return pathMatch[1];
        }

        // Extract from URL query like: http://localhost/pickup/verify?code=VRF-XXXXXX
        const queryMatch = url.match(/[?&]code=([^&]+)/);
        if (queryMatch) {
            return queryMatch[1];
        }

        return null;
    }

    // Helper function to add timeout to promises
    function withTimeout(promise, timeoutMs, errorMessage) {
        return Promise.race([
            promise,
            new Promise((_, reject) =>
                setTimeout(() => reject(new Error(errorMessage)), timeoutMs)
            )
        ]);
    }

    async function startScanning() {
        console.log('Starting QR scanner...');
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            rememberLastUsedCamera: true
        };

        try {
            // Check if Html5Qrcode is loaded
            if (typeof Html5Qrcode === 'undefined') {
                throw new Error('HTML5 QR Code library not loaded');
            }

            // Check if site is served over HTTPS (required for camera on mobile)
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                console.warn('Site is not served over HTTPS. Camera access may be blocked on mobile.');
                showHttpWarning();
                return;
            }

            // Check if camera API is available
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Camera API not available. Please use a modern browser (Chrome, Safari, Firefox) on iOS or Android.');
            }

            html5QrcodeScanner = new Html5Qrcode("reader");

            // Show loading state
            const readerDiv = document.getElementById('reader');
            readerDiv.innerHTML = '<div class="text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div><p class="text-gray-600">Requesting camera permission...</p><p class="text-xs text-gray-500 mt-2">Please allow camera access when prompted</p></div>';

            // Get available cameras with 15 second timeout
            console.log('Getting cameras...');
            let cameras;
            try {
                cameras = await withTimeout(
                    Html5Qrcode.getCameras(),
                    15000,
                    'Camera permission request timed out. Please check if you allowed camera access and try again.'
                );
                console.log('Available cameras:', cameras);
            } catch (timeoutError) {
                console.error('Camera request timeout or error:', timeoutError);
                throw timeoutError;
            }

            if (cameras && cameras.length) {
                // Prefer rear camera (usually has environment facingMode)
                const cameraId = cameras.find(camera =>
                    camera.facingMode === 'environment'
                )?.id || cameras[0].id;

                console.log('Using camera:', cameraId);
                currentCameraId = cameraId;

                // Reset reader div
                readerDiv.innerHTML = '';

                // Start scanning with timeout
                await withTimeout(
                    html5QrcodeScanner.start(
                        cameraId,
                        config,
                        onScanSuccess,
                        onScanFailure
                    ),
                    10000,
                    'Camera initialization timed out. The camera may be in use by another app.'
                );

                console.log('Scanner started successfully');
                // Update UI
                document.getElementById('startButton').classList.add('hidden');
                document.getElementById('stopButton').classList.remove('hidden');
                document.getElementById('switchButton').classList.remove('hidden');
            } else {
                throw new Error("No cameras found. Please check your device camera.");
            }
        } catch (error) {
            console.error("Error starting scanner:", error);
            console.error('Error details:', error.message);

            let errorMessage = 'Unable to start camera. ';
            if (error.message.includes('NotAllowedError') || error.message.includes('Permission denied')) {
                errorMessage += 'Camera permission was denied. Please allow camera access in your browser settings and try again.';
            } else if (error.message.includes('No cameras found')) {
                errorMessage += 'No camera detected on this device.';
            } else if (error.message.includes('NotReadableError')) {
                errorMessage += 'Camera is already in use by another application.';
            } else if (error.message.includes('timed out')) {
                errorMessage += error.message;
            } else {
                errorMessage += error.message;
            }

            showError(errorMessage);

            // Reset reader div to show error
            const readerDiv = document.getElementById('reader');
            readerDiv.innerHTML = '<div class="text-center p-4"><div class="text-red-500 text-4xl mb-4">📷</div><p class="text-red-600 text-sm font-medium mb-2">Camera Access Failed</p><p class="text-gray-600 text-xs mb-3">' + errorMessage + '</p><p class="text-gray-500 text-xs">You can still use manual entry below</p></div>';
        }
    }

    function showHttpWarning() {
        const readerDiv = document.getElementById('reader');
        readerDiv.innerHTML = '<div class="text-center p-4"><div class="text-yellow-500 text-4xl mb-4">⚠️</div><p class="text-yellow-700 text-sm font-medium mb-2">HTTPS Required for Camera</p><p class="text-gray-600 text-xs mb-3">Mobile browsers require HTTPS for camera access. Please access this site via HTTPS.</p><p class="text-gray-500 text-xs">You can still use manual entry below</p></div>';
        showError('Mobile browsers require HTTPS for camera access. Please use manual entry or access via HTTPS.');
    }

    async function stopScanning() {
        if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
            try {
                await html5QrcodeScanner.stop();
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;

                // Update UI
                document.getElementById('startButton').classList.remove('hidden');
                document.getElementById('stopButton').classList.add('hidden');
                document.getElementById('switchButton').classList.add('hidden');
            } catch (error) {
                console.error("Error stopping scanner:", error);
            }
        }
    }

    async function switchCamera() {
        if (!html5QrcodeScanner || !html5QrcodeScanner.isScanning) return;

        try {
            // Stop current scanner
            await stopScanning();

            // Start with next camera
            setTimeout(() => {
                startScanning();
            }, 500);
        } catch (error) {
            console.error("Error switching camera:", error);
            showError('Unable to switch camera. Please try again.');
        }
    }

    function verifyPickup(code) {
        showLoading();

        fetch('/pickup/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                verification_code: code,
                pickup_code: code,
                quality_rating: 5,
                notes: 'QR code scan'
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                // Redirect to feedback page if verification_id is returned
                if (data.verification_id) {
                    window.location.href = '/pickup/feedback/' + data.verification_id;
                } else {
                    showSuccess();
                }
            } else {
                showError(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showError('Network error. Please check your connection and try again.');
        });
    }

    // Manual form submission
    document.getElementById('manualForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('verificationCode').value.trim();
        if (code) {
            verifyPickup(code);
        }
    });

    function showSuccess() {
        document.getElementById('successModal').classList.remove('hidden');
        stopScanning();
    }

    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
    }

    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
            stopScanning();
        }
    });

    // Make functions globally accessible
    window.startScanning = startScanning;
    window.stopScanning = stopScanning;
    window.switchCamera = switchCamera;
    window.verifyPickup = verifyPickup;

    // Add event listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const startBtn = document.getElementById('startButton');
        const stopBtn = document.getElementById('stopButton');
        const switchBtn = document.getElementById('switchButton');
        const manualForm = document.getElementById('manualForm');

        console.log('DOM loaded, attaching event listeners...');
        console.log('Start button found:', !!startBtn);
        console.log('Start scanning function:', typeof window.startScanning);

        if (startBtn) {
            console.log('Adding click event listener to start button...');
            startBtn.addEventListener('click', function() {
                console.log('Start button clicked!');
                if (typeof startScanning === 'function') {
                    startScanning();
                } else {
                    console.error('startScanning is not a function:', startScanning);
                }
            });
        } else {
            console.error('Start button not found!');
        }

        if (stopBtn) {
            stopBtn.addEventListener('click', stopScanning);
        }
        if (switchBtn) {
            switchBtn.addEventListener('click', switchCamera);
        }
        if (manualForm) {
            manualForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const code = document.getElementById('verificationCode').value.trim();
                if (code) {
                    verifyPickup(code);
                }
            });
        }

        // Make closeErrorModal globally accessible and add event listener
        window.closeErrorModal = closeErrorModal;
        if (document.getElementById('closeErrorBtn')) {
            document.getElementById('closeErrorBtn').addEventListener('click', closeErrorModal);
        }
    });
</script>
@endsection