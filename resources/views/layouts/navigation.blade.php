{{-- resources/views/layouts/app.blade.php --}}
<nav x-data="{ open: false, scrolled: false }"
     @scroll.window="scrolled = window.pageYOffset > 20"
     :class="{'shadow-lg': scrolled}"
     class="sticky top-0 z-50 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">ET-Quizzes</span>
                </a>

                <!-- Desktop Navigation -->
                @auth
                    <div class="hidden md:flex md:ml-10 md:space-x-1">
                        @if(auth()->user()->isHR())
                            <x-nav-link :href="route('hr.dashboard')" :active="request()->routeIs('hr.dashboard')" icon="dashboard">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('hr.employees.index')" :active="request()->routeIs('hr.employees.*')" icon="users">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Employees
                            </x-nav-link>
                            <x-nav-link :href="route('hr.divisions.index')" :active="request()->routeIs('hr.divisions.*')" icon="building">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Divisions
                            </x-nav-link>
                             <x-nav-link :href="route('hr.categories.index')" :active="request()->routeIs('hr.categories.*')" icon="building">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Category
                            </x-nav-link>
                            <x-nav-link :href="route('hr.quizzes.index')" :active="request()->routeIs('hr.quizzes.*')" icon="quiz">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Quizzes
                            </x-nav-link>
                            <x-nav-link :href="route('hr.reports.index')" :active="request()->routeIs('hr.reports.*')" icon="chart">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Reports
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')" icon="dashboard">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('employee.quizzes.join')" :active="request()->routeIs('employee.quizzes.join')" icon="join">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Join Quiz
                            </x-nav-link>
                            <x-nav-link :href="route('employee.quizzes.history')" :active="request()->routeIs('employee.quizzes.history')" icon="history">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                History
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                @auth
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </button>

                    <!-- User Dropdown -->
                    <div class="hidden md:flex items-center" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left hidden lg:block">
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 top-16 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>

                            <!-- Profile link untuk SEMUA user (HR dan Employee) -->
                            @auth
                                @if(auth()->user()->isHR())
                                    <a href="{{ route('hr.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile Settings
                                    </a>
                                @else
                                    <a href="{{ route('employee.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile Settings
                                    </a>
                                @endif
                            @endauth

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="open = !open" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'block': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{'block': open, 'hidden': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-cloak class="md:hidden bg-white border-t border-gray-100 shadow-lg">
        @auth
            <div class="px-4 py-3 space-y-1">
                @if(auth()->user()->isHR())
                    <x-responsive-nav-link :href="route('hr.dashboard')" :active="request()->routeIs('hr.dashboard')">
                        📊 Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('hr.employees.index')" :active="request()->routeIs('hr.employees.*')">
                        👥 Employees
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('hr.divisions.index')" :active="request()->routeIs('hr.divisions.*')">
                        🏢 Divisions
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('hr.categories.index')" :active="request()->routeIs('hr.categories.*')">
                        🏢 Category
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('hr.quizzes.index')" :active="request()->routeIs('hr.quizzes.*')">
                        📝 Quizzes
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('hr.reports.index')" :active="request()->routeIs('hr.reports.*')">
                        📈 Reports
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')">
                        📊 Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('employee.quizzes.join')" :active="request()->routeIs('employee.quizzes.join')">
                        🔑 Join Quiz
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('employee.quizzes.history')" :active="request()->routeIs('employee.quizzes.history')">
                        📜 History
                    </x-responsive-nav-link>
                @endif
            </div>

            <div class="px-4 py-3 border-t border-gray-100">
                <!-- Profile link untuk mobile -->
                @if(auth()->user()->isHR())
                    <x-responsive-nav-link :href="route('hr.profile.edit')">
                        ⚙️ Profile Settings
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('employee.profile.edit')">
                        ⚙️ Profile Settings
                    </x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                        🚪 Sign Out
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>
