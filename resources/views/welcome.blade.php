<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('appleicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <title>Checkpoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&family=Open+Sans:wght@300;400;600&display=swap');

        .font-crimson {
            font-family: 'Crimson Text', serif;
        }

        .font-open {
            font-family: 'Open Sans', sans-serif;
        }

        .harvard-red {
            background-color: #A41034;
        }

        .login-blue {
            background-color: #A41034;
        }

        .harvard-red-text {
            color: #A41034;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(20, 16, 120, 0.9) 0%, rgba(21, 21, 10, 0.2) 100%);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen font-open">
    <!-- Navigation -->
    <div class="navbar bg-white shadow-lg border-b-2 border-red-100">
        <div class="navbar-start">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden">
                    <i class="fas fa-bars text-xl text-red-800"></i>
                </label>
                <ul tabindex="0"
                    class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-lg w-52 border">
                    <li><a href="#" class="text-gray-700 hover:text-blue-800">Home</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-blue-800">About</a></li>
                    <li><a href="#features" class="text-gray-700 hover:text-blue-800">Features</a></li>
                    <li><a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-800">Get Started</a></li>
                </ul>
            </div>
            <a class="btn btn-ghost normal-case text-xl font-crimson text-gray-800 font-bold">
                Checkpoint Shoes</a>
            {{-- <i class="fas fa-university mr-2"></i> --}}
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li><a class="font-medium text-gray-700 hover:text-blue-800">Home</a></li>
                <li><a class="font-medium text-gray-700 hover:text-blue-800">About</a></li>
                <li><a href="#features" class="font-medium text-gray-700 hover:text-blue-800">Features</a></li>
                <li><a href="{{ route('login') }}" class="font-medium text-gray-700 hover:text-blue-800">Get Started</a>
                </li>
            </ul>
        </div>
        <div class="navbar-end">
            <a href="{{ route('login') }}" class="btn login-blue text-white hover:bg-red-900 border-none">Login</a>
        </div>
    </div>

    <!-- Hero Section with Harvard-style banner -->
    <div class="relative min-h-[70vh] bg-cover bg-center"
        style="background-image: url('{{ asset('background.jpg') }}');">
        <div class="absolute inset-0 gradient-overlay"></div>
        <div class="relative hero min-h-[70vh]">
            <div class="hero-content text-center text-white p-4 md:p-8">
                <div class="max-w-4xl">
                    <h1 class="text-4xl md:text-4xl font-bold font-crimson mb-4">
                        Welcome to Checkpoint Shoes – Over 50 Years of Quality and Craftsmanship
                    </h1>
                    <h2 class="text-xl md:text-3xl mb-6 font-crimson opacity-90">
                        Proudly operating since the 1970s
                    </h2>
                    <p class="text-lg md:text-xl mb-8 max-w-4xl mx-auto leading-relaxed">
                        {{-- Checkpoint Shoes by Felicel Shoe Manufacturing is a trusted name in the Philippine shoe
                        industry—manufacturing, retailing, and wholesaling a wide range of authentic footwear and
                        garments. Founded by first-generation shoemakers and now led by Mr. Neil Nepomuceno, our brand
                        continues its legacy of craftsmanship and customer care. --}}
                        With over 50 years in the Philippine shoe industry, Checkpoint Shoes by Felicel Shoe
                        Manufacturing continues to deliver authentic footwear and garments — from dress and school shoes
                        to safety and nursing footwear.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}"
                            class="btn btn-lg bg-white text-blue-800 hover:bg-gray-100 border-none font-semibold">
                            Get Started
                        </a>
                        <a href="#features"
                            class="btn btn-lg btn-outline text-white border-white hover:bg-white hover:text-blue-800">
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traditions Section -->
    <div class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold font-crimson text-blue-500 md:harvard-red-text mb-4">A Legacy
                    of Footwear
                    Excellence Since the 1970s</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto italic font-extralight">
                    “Honoring the Past. Powering the Present. Stepping Into the Future.”
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-scroll text-3xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-xl font-bold font-crimson mb-2">Heritage of Craftsmanship</h3>
                    <p class="text-gray-600"><span>Over 50 years of shoemaking rooted in Filipino tradition.
                            From our beginnings in the 1970s under Barry Shoes to today’s Checkpoint Shoes brand, we
                            carry forward the legacy of first-generation shoemakers — combining skill, passion, and
                            family
                            tradition.</span> </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-globe text-3xl text-blue-300"></i>
                    </div>
                    <h3 class="text-xl font-bold font-crimson mb-2">Everyday Impact</h3>
                    <p class="text-gray-600">Serving communities, one pair at a time.
                        <span>With hundreds of designs, our products
                            support students, professionals, families, and frontline workers across the
                            Philippines.</span>
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lightbulb text-3xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-bold font-crimson mb-2">Driven by Innovation</h3>
                    <p class="text-gray-600">Merging tradition with modern retail technology.
                        We continue to evolve by integrating online inventory system, and digital
                        payment methods — ensuring efficiency, accuracy, and better service.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 font-crimson harvard-red-text">
                Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <div class="card bg-white shadow-xl border border-gray-200 hover:shadow-2xl transition-shadow">
                    <figure class="px-10 pt-10">
                        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fas fa-boxes-stacked text-4xl harvard-red-text"></i>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title text-xl font-crimson">Manage Inventory In Real-Time</h3>
                        <p class="text-gray-500">
                            Keep track of stock levels and ensure you never run out of your best sellers.
                        </p>
                    </div>
                </div>

                {{-- <div class="card bg-white shadow-xl border border-gray-200 hover:shadow-2xl transition-shadow">
                    <figure class="px-10 pt-10">
                        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fas fa-group-arrows-rotate text-4xl harvard-red-text"></i>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title text-xl font-crimson">Seamless eCommerce Integrations</h3>
                        <p class="text-gray-500">
                            Easily sync your inventory across top online sales platforms, including Shoppee, Lazada, and
                            TikTok, making it simple to manage both your online and in-store stock.
                        </p>
                    </div>
                </div> --}}

                <div class="card bg-white shadow-xl border border-gray-200 hover:shadow-2xl transition-shadow">
                    <figure class="px-10 pt-10">
                        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fa-brands fa-salesforce text-4xl harvard-red-text"></i>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h3 class="card-title text-xl font-crimson">Track and Manage Sales With Ease</h3>
                        <p class="text-gray-500">
                            From daily transactions to detailed sales reports, our system helps you keep your business
                            on track.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    {{-- <div class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 font-crimson harvard-red-text">
                Voices from Harvard
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="card bg-gray-50 shadow-lg border">
                    <div class="card-body">
                        <div class="flex items-center mb-4">
                            <div class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80"
                                        alt="Professor" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold font-crimson">Professor James Mitchell</h3>
                                <p class="text-sm text-gray-600">Kennedy School of Government</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic">
                            "This portal exemplifies Harvard's dedication to innovation in education. It seamlessly
                            integrates
                            our academic traditions with cutting-edge technology."
                        </p>
                        <div class="mt-3 text-amber-500">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>

                <div class="card bg-gray-50 shadow-lg border">
                    <div class="card-body">
                        <div class="flex items-center mb-4">
                            <div class="avatar">
                                <div class="w-12 rounded-full">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616c5e83a78?auto=format&fit=crop&q=80"
                                        alt="Student" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold font-crimson">Emily Rodriguez</h3>
                                <p class="text-sm text-gray-600">Graduate Student, Harvard Business School</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic">
                            "The platform's intuitive design and comprehensive features have transformed how I engage
                            with my coursework and connect with the Harvard community."
                        </p>
                        <div class="mt-3 text-amber-500">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Call to Action -->
    <div class="py-16 harvard-red text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 font-crimson">
                Let’s get started – Your next sale is just a click away!
            </h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                Start browsing, selling, and managing your inventory like a pro. We’re here to make running your shoe
                and garment store easier, smarter, and more efficient.
            </p>
            <a href="{{ route('login') }}"
                class="btn btn-lg bg-white text-red-800 hover:bg-gray-100 border-none font-semibold">
                Access it now!
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer p-10 bg-gray-900 text-gray-300 justify-evenly">
        <div>
            <span class="footer-title text-white font-crimson">Quick Links</span>
            <a class="link link-hover">Home</a>
            <a class="link link-hover">Dashboard</a>
            <a class="link link-hover">Inventory Management</a>
            <a class="link link-hover">Reports</a>
        </div>
        <div>
            <span class="footer-title text-white font-crimson">Checkpoint Details</span>
            <a class="link link-hover">Features</a>
            <a class="link link-hover">Technical Support</a>
            <a class="link link-hover">User Guidelines</a>
            <a class="link link-hover">Privacy & Security</a>
        </div>
        <div>
            <span class="footer-title text-white font-crimson">CONTACT OR VISIT US</span>
            <a class="link link-hover">#325 M.A. Street, Brgy. San Roque, Marikina City</a><iframe
                src="https://www.google.com/maps/embed?pb=!3m2!1sen!2sph!4v1758470274215!5m2!1sen!2sph!6m8!1m7!1sjUX36HvkV44xE2fAgfYowg!2m2!1d14.62575226224081!2d121.0981233589207!3f262.52615576382146!4f7.153406606787058!5f0.4003161831622405"
                width="300" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            <a class="link link-hover">info@checkpoint.business</a>
            <a class="link link-hover">+(02) 7006 4700</a>
            <div class="grid grid-flow-col gap-4 mt-2">
                <a><i class="fab fa-facebook-f text-lg hover:text-blue-400"></i></a>
                <a><i class="fab fa-tiktok text-lg hover:text-blue-400"></i></a>
                {{-- <a><i class="fab fa-linkedin text-lg hover:text-red-400"></i></a> --}}
                {{-- <a><i class="fab fa-youtube text-lg hover:text-red-400"></i></a> --}}
            </div>
        </div>
    </footer>
    <footer class="footer footer-center p-4 bg-black text-gray-400">

        <div>
            <button onclick="terms_modal.showModal()" class="text-white hover:text-base text-sm cursor-pointer">Terms
                & Conditions</button>
            <p class="font-crimson">© 2025 Checkpoint. All rights reserved.</p>
        </div>
    </footer>

    <!-- Terms and Conditions Modal -->
    <dialog id="terms_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-11/12 max-w-5xl max-h-[90vh] overflow-y-auto">

            <!-- Header -->
            <div class="text-center border-b pb-4">
                <h1 class="text-3xl font-bold text-gray-800">Terms and Conditions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Online Sales and Inventory: Checkpoint</p>
            </div>

            <!-- Section 1 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">1. Use of the System</h2>
                <p class="text-gray-700 mt-2">
                    By using this system, you agree to comply with these Terms and Conditions. Please ensure all order
                    and account
                    details you provide are accurate and complete. Misuse of the system may result in suspension or
                    termination of access.
                </p>
            </div>

            <!-- Section 2 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">2. Account and Security</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                    <li>Report any unauthorized access or suspicious activity immediately.</li>
                    <li>The system owner reserves the right to suspend accounts that violate these terms.</li>
                </ul>
            </div>

            <!-- Section 3 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">3. Orders and Payments</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>All orders must be reviewed carefully before confirmation.</li>
                    <li>Accepted payment methods include <span class="font-semibold">Cash</span> and <span
                            class="font-semibold">GCash (Scan to Pay)</span>.</li>
                    <li>For GCash payments, please ensure your <span class="font-semibold">reference number</span> is
                        correct before confirming.</li>
                    <li>Orders may be canceled if payment details are invalid or incomplete.</li>
                </ul>
            </div>

            <!-- Section 4 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">4. Delivery and Pick-Up</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>Estimated delivery times may vary depending on location and availability.</li>
                    <li>Additional delivery charges may apply outside standard service areas.</li>
                    <li>For in-store pick-up, please present your order confirmation or reference number.</li>
                </ul>
            </div>

            <!-- Section 5 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">5. Cancellations and Refunds</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>Orders can be canceled within the allowed timeframe before dispatch.</li>
                    <li>Refunds are issued only for defective products, incorrect charges, or verified payment errors.
                    </li>
                    <li>Completed and verified transactions are non-refundable.</li>
                </ul>
            </div>

            <!-- Section 6 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">6. Data Privacy</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>Your personal data is collected and processed in accordance with our Privacy Policy.</li>
                    <li>We do not share or sell your personal information to third parties.</li>
                    <li>Data is used only for processing orders and improving services.</li>
                </ul>
            </div>

            <!-- Section 7 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">7. System Usage</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>Do not attempt to misuse, hack, or disrupt the system.</li>
                    <li>The system may occasionally be unavailable due to maintenance or updates.</li>
                    <li>The merchant may suspend or terminate access for any misuse.</li>
                </ul>
            </div>

            <!-- Section 8 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">8. Liability</h2>
                <ul class="list-disc pl-6 text-gray-700 mt-2 space-y-1">
                    <li>We are not responsible for losses caused by technical issues, delays, or service interruptions.
                    </li>
                    <li>Our liability is limited to the total amount paid for the affected transaction.</li>
                </ul>
            </div>

            <!-- Section 9 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">9. Governing Law</h2>
                <p class="text-gray-700 mt-2">
                    These Terms and Conditions are governed by the laws of the Republic of the Philippines. Any disputes
                    will be handled
                    in accordance with applicable legal procedures.
                </p>
            </div>

            <!-- Section 10 -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 ">10. Contact Us</h2>
                <p class="text-gray-700 mt-2">
                    For questions, feedback, or concerns, please contact our support team or visit our customer service
                    section.
                </p>
            </div>


            <!-- Modal Actions -->
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-primary bg-gray-950">Close</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</body>

</html>
