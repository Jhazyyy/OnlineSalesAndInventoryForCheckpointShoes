<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Terms and Conditions - Checkpoint</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet" type="text/css" />
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation -->
    <div class="navbar bg-white shadow-lg border-b-2 border-red-100">
        <div class="navbar-start">
            <a href="{{ url('/') }}" class="btn btn-ghost normal-case text-xl font-bold text-gray-800">
                Checkpoint Shoes
            </a>
        </div>
        <div class="navbar-end">
            <a href="{{ route('login') }}" class="btn bg-red-800 text-white hover:bg-red-900 border-none">Login</a>
        </div>
    </div>

    <div class="py-10 px-6 lg:px-12 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-xl p-8 space-y-6">
            
            <!-- Header -->
            <div class="text-center border-b pb-4">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Terms and Conditions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Online Sales and Inventory System</p>
            </div>

            <!-- Section 1 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">1. Use of the System</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">
                    By using this system, you agree to comply with these Terms and Conditions. Please ensure all order and account
                    details you provide are accurate and complete. Misuse of the system may result in suspension or termination of access.
                </p>
            </div>

            <!-- Section 2 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">2. Account and Security</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                    <li>Report any unauthorized access or suspicious activity immediately.</li>
                    <li>The system owner reserves the right to suspend accounts that violate these terms.</li>
                </ul>
            </div>

            <!-- Section 3 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">3. Orders and Payments</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>All orders must be reviewed carefully before confirmation.</li>
                    <li>Accepted payment methods include <span class="font-semibold">Cash</span> and <span class="font-semibold">GCash (Scan to Pay)</span>.</li>
                    <li>For GCash payments, please ensure your <span class="font-semibold">reference number</span> is correct before confirming.</li>
                    <li>Orders may be canceled if payment details are invalid or incomplete.</li>
                </ul>
            </div>

            <!-- Section 4 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">4. Delivery and Pick-Up</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>Estimated delivery times may vary depending on location and availability.</li>
                    <li>Additional delivery charges may apply outside standard service areas.</li>
                    <li>For in-store pick-up, please present your order confirmation or reference number.</li>
                </ul>
            </div>

            <!-- Section 5 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">5. Cancellations and Refunds</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>Orders can be canceled within the allowed timeframe before dispatch.</li>
                    <li>Refunds are issued only for defective products, incorrect charges, or verified payment errors.</li>
                    <li>Completed and verified transactions are non-refundable.</li>
                </ul>
            </div>

            <!-- Section 6 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">6. Data Privacy</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>Your personal data is collected and processed in accordance with our Privacy Policy.</li>
                    <li>We do not share or sell your personal information to third parties.</li>
                    <li>Data is used only for processing orders and improving services.</li>
                </ul>
            </div>

            <!-- Section 7 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">7. System Usage</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>Do not attempt to misuse, hack, or disrupt the system.</li>
                    <li>The system may occasionally be unavailable due to maintenance or updates.</li>
                    <li>The merchant may suspend or terminate access for any misuse.</li>
                </ul>
            </div>

            <!-- Section 8 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">8. Liability</h2>
                <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                    <li>We are not responsible for losses caused by technical issues, delays, or service interruptions.</li>
                    <li>Our liability is limited to the total amount paid for the affected transaction.</li>
                </ul>
            </div>

            <!-- Section 9 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">9. Governing Law</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">
                    These Terms and Conditions are governed by the laws of the Republic of the Philippines. Any disputes will be handled
                    in accordance with applicable legal procedures.
                </p>
            </div>

            <!-- Section 10 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">10. Contact Us</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">
                    For questions, feedback, or concerns, please contact our support team or visit our customer service section.
                </p>
            </div>

            <!-- Back to Home Button -->
            <div class="pt-6 border-t">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Home
                </a>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer footer-center p-4 bg-black text-gray-400">
        <div>
            <p>© 2025 Checkpoint. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
