<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Listing Verification - MyFoodshare</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 10px;
        }
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .status-icon.valid {
            background: #DCFCE7;
            color: #16A34A;
        }
        .status-icon.invalid {
            background: #FEE2E2;
            color: #DC2626;
        }
        .status-icon svg {
            width: 40px;
            height: 40px;
        }
        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1F2937;
        }
        .message {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 30px;
        }
        .qr-section {
            margin: 30px 0;
        }
        .qr-container {
            background: #F9FAFB;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            display: inline-block;
        }
        .qr-label {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .food-info {
            background: #F9FAFB;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
        }
        .food-info h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #1F2937;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .info-label {
            color: #6B7280;
        }
        .info-value {
            font-weight: 500;
            color: #1F2937;
        }
        .verification-code {
            background: #F3F4F6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            letter-spacing: 2px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #3B82F6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563EB;
        }
        .btn-secondary {
            background: #F3F4F6;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #E5E7EB;
        }
        @media (max-width: 600px) {
            .verification-container {
                padding: 20px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header">
            <div class="logo">🍽️ MyFoodshare</div>
        </div>

        <div class="status-icon {{ $valid ? 'valid' : 'invalid' }}">
            @if ($valid)
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            @else
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            @endif
        </div>

        <h1>
            @if ($valid)
                Verification Successful!
            @else
                Verification Failed
            @endif
        </h1>

        <div class="message">
            {{ $message }}
        </div>

        @if ($valid)
            <div class="qr-section">
                <div class="qr-container" id="qrcode"></div>
            </div>

            <div class="food-info">
                <h3>Food Details</h3>
                <div class="info-row">
                    <span class="info-label">Food Item:</span>
                    <span class="info-value">{{ $foodListing->food_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Quantity:</span>
                    <span class="info-value">{{ $foodListing->quantity }} {{ $foodListing->unit }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Restaurant:</span>
                    <span class="info-value">{{ $foodListing->restaurantProfile->restaurant_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pickup Location:</span>
                    <span class="info-value">{{ $foodListing->pickup_location }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Expiry:</span>
                    <span class="info-value">{{ $foodListing->expiry_date->format('M d, Y') }} @if($foodListing->expiry_time) at {{ $foodListing->expiry_time->format('H:i') }} @endif</span>
                </div>
            </div>

            <div class="verification-code">
                Code: {{ $foodListing->getVerificationCode() }}
            </div>
        @endif

        <div class="action-buttons">
            <a href="{{ route('food-listings.index') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" clip-rule="evenodd"></path>
                </svg>
                Browse More Food
            </a>
            <a href="#" class="btn btn-secondary" onclick="window.print()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                </svg>
                Print Verification
            </a>
        </div>
    </div>

    <script>
        // Generate QR code if verification is valid
        @if ($valid)
            document.addEventListener('DOMContentLoaded', function() {
                // Generate unique ID for this verification
                const verificationData = {
                    listing_id: "{{ $foodListing->id }}",
                    food_name: "{{ $foodListing->food_name }}",
                    quantity: "{{ $foodListing->quantity }} {{ $foodListing->unit }}",
                    restaurant: "{{ $foodListing->restaurantProfile->restaurant_name }}",
                    pickup_location: "{{ $foodListing->pickup_location }}",
                    expiry: "{{ $foodListing->expiry_date->format('Y-m-d') }}",
                    verification_code: "{{ $foodListing->getVerificationCode() }}",
                    verified_at: "{{ now()->toISOString() }}"
                };

                const qrcodeDiv = document.getElementById('qrcode');
                qrcodeDiv.innerHTML = '';

                new QRCode(qrcodeDiv, {
                    text: JSON.stringify(verificationData),
                    width: 200,
                    height: 200,
                    colorDark: "#1F2937",
                    colorLight: "#FFFFFF",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
        @endif
    </script>
</body>
</html>