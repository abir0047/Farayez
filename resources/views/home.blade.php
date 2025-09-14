@extends('layouts.app')

@section('content')
    <section class="relative w-full"
        style="height:300px; background: url('/hero-bg.png') center center / cover no-repeat; color: white;">
        <div class="absolute inset-0 w-full h-full" style="background:rgba(0,0,0,0.6);"></div>
        <div class="container mx-auto h-full flex flex-col justify-center relative z-10">
            <div class="pt-10 flex flex-col gap-8" style="max-width:450px;">
                <h2 class="text-2xl md:text-3xl">মুসলিম উত্তরাধিকার আইন অনুসরণী সম্পত্তি বণ্টন করুন মুহূর্তেই!</h2>
                <a href="{{ route('calculator') }}"
                    class="text-white px-6 py-3 text- border-0 rounded-sm self-start btn-green">ক্যালকুলেট
                    করুন</a>
            </div>
            <div class="flex flex-row mx-auto mt-auto" style="border-radius:0; max-width:620px; width:100%; height:72px;">
                <a href="#inheritance-law"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 text-center text-lg"
                    style="border-radius:0; border-right:0.2px solid #e5e7eb69;">উত্তরাধিকার আইন</a>
                <a href="#muslim-farayez"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 text-center text-lg"
                    style="border-radius:0; border-right:0.5px solid #e5e7eb69;">মুসলিম ফারায়েজ</a>
                <a href="#islamic-quote"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 text-center text-lg"
                    style="border-radius:0;">ইসলামিক উক্তি</a>
            </div>
        </div>
    </section>

    <section id="inheritance-law" class="bg-[#03442C] py-8 text-white" style="scroll-margin-top:25vh;">
        <div class="container mx-auto">
            <h3 class="text-lg font-bold mb-2 text-[#C1FF72] leading-normal">উত্তরাধিকার আইন</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p>মুসলিম উত্তরাধিকার আইন, যা ফারায়েজ নামে পরিচিত, একটি পবিত্র ও সুনির্দিষ্ট বিধান যা ইসলামে সম্পত্তির
                        সুষ্ঠু বণ্টনের জন্য অপরিহার্য।</p>
                </div>
                <div>
                    <p>এটি কেবল একটি আইন নয়, বরং একটি ঐশী বিধান যা প্রত্যেক উত্তরাধিকারীর অধিকার নিশ্চিত করে। এই ব্যবস্থা
                        মুসলিমদের জন্য মৃত ব্যক্তির রেখে যাওয়া সম্পদ থেকে তাদের প্রাপ্য অংশ সঠিকভাবে পাওয়ার এক নির্ভরযোগ্য
                        মাধ্যম।</p>
                </div>
                <div>
                    <p>আমাদের অ্যাপটি ফারায়েজের এই জটিল হিসাবকে সহজ করে তুলেছে, যাতে যেকোনো ব্যক্তি সহজেই এবং নির্ভুলভাবে
                        তাদের উত্তরাধিকারের হিসেব করতে পারে। তথ্যসূত্র: আল কোরআন, সূরা নিসা, আয়াত ৭-১২।</p>
                </div>
            </div>
        </div>
    </section>

    <section id="muslim-farayez" class="bg-[#F5FFE8] py-8 text-black" style="scroll-margin-top:25vh;">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <button
                    class="farayez-tab text-xl md:text-2xl md:px-8 md:py-4 md:min-h-[80px] flex items-center justify-center text-center active"
                    data-tab="tab1">
                    মুসলিম ফারায়েজ ইতিহাস
                </button>
                <button
                    class="farayez-tab text-xl md:text-2xl md:px-8 md:py-4 md:min-h-[80px] flex items-center justify-center text-center"
                    data-tab="tab2">
                    প্রবর্তন
                </button>
                <button
                    class="farayez-tab text-xl md:text-2xl md:px-8 md:py-4 md:min-h-[80px] flex items-center justify-center text-center"
                    data-tab="tab3">
                    বর্তমান বিশ্বে
                </button>
            </div>

            <div>
                <div id="tab1" class="farayez-content mb-8">
                    <p>ইসলামের আবির্ভাবের আগে আরবে উত্তরাধিকারের কোনো সুনির্দিষ্ট নিয়ম ছিল না। সম্পত্তি সাধারণত পুরুষদের
                        মধ্যে বণ্টন করা হতো এবং নারীদের কোনো অধিকার ছিল না। ইসলাম এই বৈষম্য দূর করে, কোরআন ও হাদিসের মাধ্যমে
                        নারী ও পুরুষ উভয়ের জন্য সম্পত্তির সুনির্দিষ্ট অংশ নির্ধারণ করে দেয়। ফারায়েজের মূল ভিত্তি কোরআনে
                        সুস্পষ্টভাবে বর্ণিত হয়েছে, যেখানে আল্লাহ নিজেই মৃত ব্যক্তির নিকটাত্মীয়দের জন্য অংশ নির্ধারণ করে
                        দিয়েছেন। ইসলামের প্রথম যুগে রাসূলুল্লাহ (সাঃ) এর তত্ত্বাবধানে এই আইন বাস্তবায়িত হতো। পরবর্তীতে
                        খোলাফায়ে রাশেদীন ও মুসলিম শাসকগণ এই আইনের প্রয়োগ নিশ্চিত করেন। সময়ের সাথে সাথে বিভিন্ন মাযহাব
                        (হানাফি, শাফেয়ী, মালেকী, হাম্বলী) ফারায়েজের নিয়মাবলীকে আরও বিশদ ও কাঠামোবদ্ধ করেছে, যা আজও মুসলিম
                        বিশ্বে প্রচলিত।
                    </p>
                </div>
                <div id="tab2" class="farayez-content hidden mb-8">
                    <p>মুসলমানদের সম্পত্তি বণ্টন মুসলিম উত্তরাধিকার আইন বা ফারায়েয অনুসারে হয়ে থাকে। এই আইনটি মূলত কুরআন,
                        সুন্নাহ, ইজমা এবং কিয়াসের উপর ভিত্তি করে গঠিত। বাংলাদেশের প্রেক্ষাপাপটে মুসলিম উত্তরাধিকারের বিষয়টি
                        মুসলিম পারিবারিক আইন অধ্যাদেশ, ১৯৬১ দ্বারাও নিয়ন্ত্রিত হয়।</p>
                </div>
                <div id="tab3" class="farayez-content hidden mb-8">
                    <p>বর্তমান বিশ্বে মুসলিম ফারায়েজ আইন বিভিন্ন দেশে বিভিন্নভাবে প্রয়োগ করা হয়। সৌদি আরব, পাকিস্তান,
                        বাংলাদেশসহ অনেক মুসলিম-সংখ্যাগরিষ্ঠ দেশে পারিবারিক ও উত্তরাধিকার সংক্রান্ত বিষয়গুলো ইসলামি শরিয়াহ
                        অনুযায়ী পরিচালিত হয় এবং এটি রাষ্ট্রীয় আইনের অংশ। এসব দেশে আদালত কোরআন ও সুন্নাহর আলোকে সম্পত্তি
                        বণ্টন করে থাকে। অন্যদিকে, তুরস্কের মতো কিছু মুসলিম দেশে উত্তরাধিকার আইন সম্পূর্ণ সিভিল কোডের ওপর
                        ভিত্তি করে তৈরি, যা শরিয়াহর চেয়ে ভিন্ন। মালয়েশিয়ার মতো দেশে দ্বৈত আইন ব্যবস্থা প্রচলিত, যেখানে
                        মুসলিমরা শরিয়াহ আদালতের মাধ্যমে তাদের উত্তরাধিকারের বিষয় নিষ্পত্তি করতে পারে। পশ্চিমা দেশগুলোতে
                        বসবাসকারী মুসলিমদের জন্য বিষয়টি আরও জটিল, কারণ তাদের সে দেশের সিভিল আইন মেনে চলতে হয়। তবে, তারা
                        চাইলে উইলের (ওসিয়ত) মাধ্যমে ইসলামি ফারায়েজ অনুযায়ী তাদের সম্পত্তি বণ্টনের ব্যবস্থা করতে পারেন, যা
                        আইনগতভাবে স্বীকৃত।</p>
                </div>
            </div>
        </div>
    </section>

    <section id="islamic-quote" class="bg-[#F8F8F8] py-8" style="scroll-margin-top:25vh;">
        <div class="container mx-auto">
            <h3 class="text-2xl font-bold mb-2 text-[#006F45] leading-normal">বানী</h3>
            <div class="swiper quote-swiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"ফারায়েজ হলো জ্ঞানের অর্ধেক।"
                                <br><span class="font-medium text-base"> - রাসূলুল্লাহ (সাঃ) (তথ্যসূত্র: ইবনে মাজাহ) </span>
                            </blockquote>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"আল্লাহর নির্ধারিত সীমা অতিক্রম
                                করে কোনো উত্তরাধিকারীর অংশ বাড়ানো বা কমানো যাবে না।" <br><span
                                    class="font-medium text-base"> - হযরত
                                    আবু বকর (রাঃ) (তথ্যসূত্র:
                                    সহিহ আল-বুখারী)</span></blockquote>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"ফারায়েজ হচ্ছে এমন একটি বিজ্ঞান
                                যা শিখলে মানুষের জীবন সরল হয় এবং সমাজে ন্যায়বিচার প্রতিষ্ঠিত হয়।" <br><span
                                    class="font-medium text-base"> - ইমাম আবু হানিফা
                                    (রহঃ)
                                    (তথ্যসূত্র: আল-ফিকহ আল-আকবর)</span></blockquote>
                        </div>
                    </div>
                </div>
                <!-- Add pagination (dots) -->
                <div class="swiper-pagination !bottom-0 !relative mt-6"></div>
            </div>
        </div>
    </section>

    <section class="bg-[#F8F8F8] py-8">
        <div class="container mx-auto">
            <h3 class="text-2xl font-bold mb-2 text-[#006F45] leading-normal">সাম্প্রতিক ব্লগ</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <a href="https://article.legalfist.com/civil-law/inheritance-law/%e0%a6%ae%e0%a7%81%e0%a6%b8%e0%a6%b2%e0%a6%bf%e0%a6%ae-%e0%a6%89%e0%a6%a4%e0%a7%8d%e0%a6%a4%e0%a6%b0%e0%a6%be%e0%a6%a7%e0%a6%bf%e0%a6%95%e0%a6%be%e0%a6%b0-%e0%a6%86%e0%a6%87%e0%a6%a8%e0%a7%87/"
                    target="_blank"
                    class="block bg-[#006F45] rounded-lg shadow overflow-hidden transition-all duration-300 hover:scale-105 transform">
                    <div class="relative">
                        <img src="http://article.legalfist.com/wp-content/uploads/2025/02/death-time-in-muslim-sharia-law-for-property-dividation.jpg"
                            alt="Muslim Sharia Law Property Division" class="w-full object-cover" />
                    </div>
                    <div class="p-6 text-center text-lg text-white">
                        মুসলিম উত্তরাধিকার আইনে সম্পত্তি বন্টনের ক্ষেত্রে মৃত্যুর সময় কেন এত গুরুত্বপূর্ণ?
                    </div>
                </a>
                <div class="grid grid-cols-1 gap-4">
                    <a href="https://article.legalfist.com/civil-law/inheritance-law/%e0%a6%b8%e0%a6%ae%e0%a7%8d%e0%a6%aa%e0%a6%a4%e0%a7%8d%e0%a6%a4%e0%a6%bf-%e0%a6%ac%e0%a6%a3%e0%a7%8d%e0%a6%9f%e0%a6%a8%e0%a7%87%e0%a6%b0-%e0%a6%af%e0%a7%87-%e0%a6%ae%e0%a7%8c%e0%a6%b2%e0%a6%bf/"
                        target="_blank"
                        class="bg-[#006F45] rounded-lg shadow overflow-hidden transition-all duration-300 hover:scale-105 transform block md:flex md:items-center md:gap-4">
                        <div class="relative md:w-48 md:flex-shrink-0 h-auto md:h-32">
                            <img src="http://article.legalfist.com/wp-content/uploads/2024/03/mistake-by-not-knowing-the-basics-of-property-distribution.jpg"
                                alt="Blog Image" class="w-full h-full object-cover" />
                        </div>
                        <div class="text-white p-4 text-center md:text-left">সম্পত্তি বণ্টনের যে মৌলিক বিষয়টি না
                            জানার কারণে আপনি হিসেবে ভুল
                            করছেন</div>
                    </a>
                    <a href="https://article.legalfist.com/civil-law/inheritance-law/%e0%a6%95%e0%a6%96%e0%a6%a8-%e0%a6%95%e0%a6%a8%e0%a7%8d%e0%a6%af%e0%a6%be-%e0%a6%b8%e0%a6%a8%e0%a7%8d%e0%a6%a4%e0%a6%be%e0%a6%a8-%e0%a6%8f%e0%a6%95%e0%a6%be%e0%a6%87-%e0%a6%aa%e0%a6%bf%e0%a6%a4/"
                        target="_blank"
                        class="bg-[#006F45] rounded-lg shadow overflow-hidden transition-all duration-300 hover:scale-105 transform block md:flex md:items-center md:gap-4">
                        <div class="relative md:w-48 md:flex-shrink-0 h-auto md:h-32">
                            <img src="http://article.legalfist.com/wp-content/uploads/2023/10/When-a-daughter-alone-can-own-the-entire-property-of-her-parents.jpg"
                                alt="Blog Image" class="w-full h-full object-cover" />
                        </div>
                        <div class="text-white p-4 text-center md:text-left">কখন কন্যা সন্তান একাই পিতা মাতার পুরো
                            সম্পত্তির মালিক হতে পারে</div>
                    </a>
                    <a href="https://article.legalfist.com/civil-law/inheritance-law/%e0%a6%ae%e0%a7%83%e0%a6%a4-%e0%a6%93%e0%a7%9f%e0%a6%be%e0%a6%b0%e0%a6%bf%e0%a6%b6%e0%a7%87%e0%a6%b0-%e0%a6%89%e0%a6%a4%e0%a7%8d%e0%a6%a4%e0%a6%b0%e0%a6%be%e0%a6%a7%e0%a6%bf%e0%a6%95%e0%a6%be%e0%a6%b0/"
                        target="_blank"
                        class="bg-[#006F45] rounded-lg shadow overflow-hidden transition-all duration-300 hover:scale-105 transform block md:flex md:items-center md:gap-4">
                        <div class="relative md:w-48 md:flex-shrink-0 h-auto md:h-32">
                            <img src="http://article.legalfist.com/wp-content/uploads/2021/12/dead-warish-property-distribution.jpg"
                                alt="Blog Image" class="w-full h-full object-cover" />
                        </div>
                        <div class="text-white p-4 text-center md:text-left">মৃত ওয়ারিশের উত্তরাধিকার</div>
                    </a>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="https://article.legalfist.com/category/civil-law/inheritance-law/" target="_blank"
                    class="text-[#006F45] text-base mb-4 md:mb-0 underline hover:text-[#006F45ab] transition-colors duration-200">আরও
                    দেখুন ></a>
            </div>
        </div>
    </section>

    <section class="bg-[#F5FFE8] py-8">
        <div class="container mx-auto">
            <h3 class="text-2xl font-bold mb-2 text-[#006F45] leading-normal">ভিডিও</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <a href="https://www.youtube.com/watch?v=7Ec5nHTTrmU" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/7Ec5nHTTrmU/maxresdefault.jpg" alt="Video 1"
                            class="w-full h-auto object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('7Ec5nHTTrmU') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=BierIiqYFUs" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/BierIiqYFUs/maxresdefault.jpg" alt="Video 2"
                            class="w-full h-auto object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('BierIiqYFUs') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=PPjkJVMRbTo" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/PPjkJVMRbTo/maxresdefault.jpg" alt="Video 3"
                            class="w-full h-auto object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('PPjkJVMRbTo') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=ujtgqHUcTQg" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/ujtgqHUcTQg/maxresdefault.jpg" alt="Video 4"
                            class="w-full h-auto object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('ujtgqHUcTQg') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=lxXF6Y4fT1c" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/lxXF6Y4fT1c/maxresdefault.jpg" alt="Video 5"
                            class="w-full h-auto object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('lxXF6Y4fT1c') }}</span>
                </a>
            </div>
            <div class="flex justify-end mt-4">
                <a href="https://www.youtube.com/playlist?list=PLP_RHoT-lzLfneOvTHFnP04gOu4DADHJs" target="_blank"
                    class="text-[#006F45] text-base mb-4 md:mb-0 underline hover:text-[#006F45ab] transition-colors duration-200">আরও
                    দেখুন ></a>
            </div>
        </div>
    </section>
@endsection
