<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ফারায়েজ | মুসলিম উত্তরাধিকার</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts: Tiro Bangla, Noto Serif Bengali & Noto Sans Bengali -->
    <link
        href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital,wght@0,400;0,700;1,400;1,700&family=Noto+Serif+Bengali:wght@400;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
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
                            <li><a href="https://legalfist.com/#about-us" target="_blank" class="hover:underline">আমাদের
                                    সম্পর্কে</a></li>
                            <li><a href="https://article.legalfist.com" target="_blank" class="hover:underline">ব্লগ</a>
                            </li>
                            <li><a href="https://wa.me/8801882689299" target="_blank"
                                    class="hover:underline">যোগাযোগ</a></li>
                            <li>
                                <a href="{{ route('calculator') }}" class="px-4 py-4 text-lg md:text-2xl btn-green"
                                    style="border-radius: 4px;">
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
                    <a href="https://legalfist.com/#about-us" target="_blank"
                        class="block py-2 px-2 rounded mobile-menu-link">আমাদের সম্পর্কে</a>
                    <a href="https://article.legalfist.com" target="_blank"
                        class="block py-2 px-2 rounded mobile-menu-link">ব্লগ</a>
                    <a href="https://wa.me/8801882689299" target="_blank"
                        class="block py-2 px-2 rounded mobile-menu-link">যোগাযোগ</a>
                    <a href="{{ route('calculator') }}" class="block px-3 py-2 mt-2 text-lg btn-green"
                        style="border-radius: 4px;">ক্যালকুলেট করুন</a>
                </div>
            </div>
        </header>
    </div>

    <!-- Main Content Outside Vue App -->
    <main class="min-h-screen flex flex-col">
        @yield('content')
    </main>

    <section class="bg-[#03442C] py-8">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
            <div class="text-white text-2xl mb-4 md:mb-0">ফারায়েজ ওয়েবসাইট নিয়ে কোনো পরামর্শ বা মতামত জানাতে চান? </div>
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSf1acUetKbdaio7JsxNH-pFsES8PE1qUV-INVxMaR7VO6mYIQ/viewform"
                target="_blank"
                class="text-white text-2xl mb-4 md:mb-0 underline hover:text-[#41ab5d] transition-colors duration-200">মতামত
            </a>
        </div>
    </section>

    <footer class="bg-[#006F45] text-white py-6 font-['Times_New_Roman']">
        <div class="container mx-auto text-center flex flex-col justify-center items-center gap-4">
            <a href="{{ route('home') }}">
                <img src="/logo.svg" alt="ফারায়েজ Logo" class="h-8 md:h-16" />
            </a>
            <hr class="border-t border-white w-full my-2">
            <p>Copyright &copy; {{ date('Y') }} Faraiz - LEGAL FIST. All rights reserved.</p>
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
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/8801882689299" target="_blank"
        class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 z-50 group">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
        </svg>
        <span
            class="absolute right-16 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-3 py-2 rounded-lg text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            WhatsApp এ যোগাযোগ করুন
        </span>
    </a>

    <script src="{{ asset('js/tabs.js') }}"></script>
    <script src="{{ asset('js/quote-slider.js') }}"></script>
</body>

</html>
