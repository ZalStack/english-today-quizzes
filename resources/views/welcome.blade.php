<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ET-Quizzes - English Today Quizzes</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="text-2xl font-bold text-indigo-600">ET-Quizzes</div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6">
                    English Today <span class="text-indigo-600">Quizzes</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Modern platform for creating, managing, and taking English quizzes in your company.
                    Streamline your assessment process with our powerful yet easy-to-use system.
                </p>
                <div class="flex justify-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold text-lg">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold text-lg">
                            Get Started
                        </a>
                    @endauth
                    <a href="#features" class="px-8 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 transition font-semibold text-lg border-2 border-indigo-600">
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Key Features</h2>
                    <p class="text-lg text-gray-600">Everything you need to manage English assessments effectively</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">📝</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Easy Quiz Creation</h3>
                        <p class="text-gray-600">Create quizzes with multiple question types including multiple choice, true/false, short answer, and essay.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Detailed Analytics</h3>
                        <p class="text-gray-600">Track employee progress with comprehensive statistics, scores, and performance reports.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">🔒</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Enrollment</h3>
                        <p class="text-gray-600">Protect quizzes with enrollment keys and passwords. Only authorized employees can access.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">⏱️</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Timed Assessments</h3>
                        <p class="text-gray-600">Set time limits for quizzes. Auto-submit when time runs out to ensure fair assessment.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">📱</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Responsive Design</h3>
                        <p class="text-gray-600">Access quizzes from any device - desktop, tablet, or smartphone. Fully responsive interface.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-8 rounded-xl shadow-md hover:shadow-lg transition">
                        <div class="text-4xl mb-4">📄</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">PDF Import</h3>
                        <p class="text-gray-600">Import questions automatically from PDF documents. Save time on manual question entry.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-2xl font-bold mb-4">ET-Quizzes</h3>
                <p class="text-gray-400 mb-6">English Today Assessment Platform</p>
                <p class="text-gray-500">&copy; {{ date('Y') }} ET-Quizzes. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
