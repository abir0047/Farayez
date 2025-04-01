<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ফারায়েজ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/vue@3"></script>
</head>

<body class="bg-white text-gray-900">
    <div id="app">
        <header class="bg-blue-900 text-white py-4">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <a href="{{ route('home') }}">
                    <h1 class="text-xl md:text-2xl font-bold">ফারায়েজ</h1>
                </a>

                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button @click="toggleMobileMenu" class="md:hidden p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:block">
                        <ul class="md:flex space-x-6">
                            <li><a href="#" class="hover:underline">আমাদের সম্পর্কে</a></li>
                            <li><a href="#" class="hover:underline">সেবাসমূহ</a></li>
                            <li><a href="#" class="hover:underline">ব্লগ</a></li>
                            <li><a href="#" class="hover:underline">জিজ্ঞাসা</a></li>
                        </ul>
                    </nav>

                    <!-- Calculator Button -->
                    <a href="{{ route('calculator') }}"
                        class="bg-blue-500 px-3 py-2 md:px-4 md:py-2 rounded text-sm md:text-base text-white">
                        ক্যালকুলেট করুন
                    </a>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden" v-show="isMobileMenuOpen">
                <div class="px-4 pt-2 pb-4 space-y-2">
                    <a href="#" class="block py-2 hover:bg-blue-800 px-2 rounded">আমাদের সম্পর্কে</a>
                    <a href="#" class="block py-2 hover:bg-blue-800 px-2 rounded">সেবাসমূহ</a>
                    <a href="#" class="block py-2 hover:bg-blue-800 px-2 rounded">ব্লগ</a>
                    <a href="#" class="block py-2 hover:bg-blue-800 px-2 rounded">জিজ্ঞাসা</a>
                </div>
            </div>
        </header>

        <main class="min-h-screen flex flex-col">
            @yield('content')
        </main>

        <footer class="bg-gray-900 text-white py-6">
            <div class="container mx-auto text-center">
                <p>Copyright &copy; 2025</p>
            </div>
        </footer>
    </div>

    <script>
        const {
            createApp
        } = Vue;
        createApp({
            data() {
                return {
                    isMobileMenuOpen: false
                }
            },
            methods: {
                toggleMobileMenu() {
                    this.isMobileMenuOpen = !this.isMobileMenuOpen;
                }
            }
        }).mount("#app");
    </script>
</body>

</html>
