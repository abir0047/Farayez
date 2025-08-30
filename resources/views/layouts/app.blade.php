<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ফারায়েজ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts: Tiro Bangla & Noto Serif Bengali -->
    <link
        href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital,wght@0,400;0,700;1,400;1,700&family=Noto+Serif+Bengali:wght@400;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/vue@3"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YN2SM6JRZ2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-YN2SM6JRZ2');
    </script>
</head>

<body class="bg-white text-gray-900">

    <!-- Vue App for Layout (Navigation only) -->
    <div id="layout-app">
        <header class="text-white py-4" style="background-color: #41AB5D;">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <a href="{{ route('home') }}">
                    <img src="/logo.svg" alt="ফারায়েজ Logo" class="h-8 md:h-16" />
                </a>

                <div class="flex items-center">
                    <!-- Mobile Menu Button -->
                    <button @click="toggleMobileMenu" class="md:hidden">
                        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:block">
                        <ul class="md:flex space-x-6 md:text-lg">
                            <li><a href="#" class="hover:underline">আমাদের সম্পর্কে</a></li>
                            <li><a href="#" class="hover:underline">ব্লগ</a></li>
                            <li><a href="#" class="hover:underline">জিজ্ঞাসা</a></li>
                            <li><a href="#" class="hover:underline">যোগাযোগ</a></li>
                            <li>
                                <a href="{{ route('calculator') }}" class="px-3 py-2 text-lg md:text-2xl text-white"
                                    style="background-color: #005E00; border-radius: 4px;">
                                    ক্যালকুলেট করুন
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden" v-show="isMobileMenuOpen">
                <div class="px-4 pt-2 pb-4 space-y-2">
                    <a href="#" class="block py-2 px-2 rounded mobile-menu-link">আমাদের সম্পর্কে</a>
                    <a href="#" class="block py-2 px-2 rounded mobile-menu-link">ব্লগ</a>
                    <a href="#" class="block py-2 px-2 rounded mobile-menu-link">জিজ্ঞাসা</a>
                    <a href="#" class="block py-2 px-2 rounded mobile-menu-link">যোগাযোগ</a>
                    <a href="{{ route('calculator') }}" class="block px-3 py-2 mt-2 text-lg text-white"
                        style="background-color: #005E00; border-radius: 4px;">ক্যালকুলেট করুন</a>
                </div>
            </div>
        </header>
    </div>

    <!-- Main Content Outside Vue App -->
    <main class="min-h-screen flex flex-col">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white py-6">
        <div class="container mx-auto text-center">
            <p>Copyright &copy; 2025</p>
        </div>
    </footer>

    <script>
        Vue.createApp({
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
        }).mount("#layout-app");
    </script>
</body>

</html>
