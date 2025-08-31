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
                    <p>জাতীয় ও আন্তর্জাতিক মুসলিম উত্তরাধিকার আইন, অ্যাংলো-মুহাম্মাদান ল, মুসলিম পারিবারিক আইন, মুসলিম
                        পারিবারিক অধ্যাদেশ ইত্যাদি।</p>
                </div>
                <div>
                    <p>পাকিস্তানের প্রেক্ষাপটে মুসলিম পরিবার আইন অধ্যাদেশ ১৯৬১ সালের মুসলিম উত্তরাধিকার আইন।</p>
                </div>
                <div>
                    <p>১৯৬১ সালের মুসলিম পরিবার আইন অধ্যাদেশ ১৯৬১ সালের মুসলিম উত্তরাধিকার আইন।</p>
                </div>
            </div>
        </div>
    </section>

    <section id="muslim-farayez" class="bg-[#F5FFE8] py-8 text-black" style="scroll-margin-top:25vh;">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <button
                    class="farayez-tab text-xl md:text-2xl px-4 md:px-8 py-4 min-h-[80px] flex items-center justify-center text-center active"
                    data-tab="tab1">
                    মুসলিম ফারায়েজ ইতিহাস
                </button>
                <button
                    class="farayez-tab text-xl md:text-2xl px-4 md:px-8 py-4 min-h-[80px] flex items-center justify-center text-center"
                    data-tab="tab2">
                    প্রবর্তন
                </button>
                <button
                    class="farayez-tab text-xl md:text-2xl px-4 md:px-8 py-4 min-h-[80px] flex items-center justify-center text-center"
                    data-tab="tab3">
                    বর্তমান বিশ্বে
                </button>
            </div>

            <div>
                <div id="tab1" class="farayez-content mb-8">
                    <p>মুসলিম ফারায়েজ ও উত্তরাধিকার আইন ইসলামের একটি গুরুত্বপূর্ণ সামাজিক ও ধর্মীয় বিধান। ইসলামের প্রথম
                        যুগ থেকেই এই আইন প্রবর্তিত হয়েছে...</p>
                </div>
                <div id="tab2" class="farayez-content hidden mb-8">
                    <p>ইসলামে ফারায়েজ আইনের প্রবর্তন হয়েছিল মানুষের মধ্যে ন্যায়বিচার প্রতিষ্ঠার জন্য...</p>
                </div>
                <div id="tab3" class="farayez-content hidden mb-8">
                    <p>বর্তমান বিশ্বে মুসলিম ফারায়েজ আইন বিভিন্ন দেশে বিভিন্নভাবে প্রয়োগ করা হয়...</p>
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
                            <blockquote class="text-center text-xl font-semibold text-black">"আল্লাহ তোমাদের সন্তানদের
                                সম্পর্কে
                                তোমাদের নির্দেশ দেন: পুরুষের ভাগ দুই নারীর সমান..." <br> (সূরা আন-নিসা, 4:11)</blockquote>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"যারা অন্যের সম্পত্তি
                                অন্যায়ভাবে
                                ভোগ করে তারা তাদের পেটে আগুন ভোগ করে..." <br> (সূরা আন-নিসা, 4:10)</blockquote>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"তোমরা উত্তরাধিকার
                                সম্পর্কিত
                                বিষয়গুলো শিক্ষা কর এবং শিক্ষা দাও..." <br> (হাদিস)</blockquote>
                        </div>
                    </div>
                    <!-- Slide 4 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"উত্তরাধিকার সম্পর্কিত
                                জ্ঞান অর্ধেক
                                ইলম..." <br> (হাদিস)</blockquote>
                        </div>
                    </div>
                    <!-- Slide 5 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"প্রত্যেক হকদারকে তার হক
                                পৌঁছে দাও..." <br> (হাদিস)</blockquote>
                        </div>
                    </div>
                    <!-- Slide 6 -->
                    <div class="swiper-slide">
                        <div class="bg-white rounded-lg shadow py-16" style="border: .5px solid #006f454d">
                            <blockquote class="text-center text-xl font-semibold text-black">"আল্লাহর বিধান অনুযায়ী
                                সম্পদ
                                বণ্টন করা ফরজ..." <br> (হাদিস)</blockquote>
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
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center font-bold text-[#005E00]">
                    সম্প্রতিক ব্লগ</div>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-[#006F45] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
                    <div class="bg-[#006F45] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
                    <div class="bg-[#006F45] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
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
                            class="w-full h-32 object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('7Ec5nHTTrmU') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=BierIiqYFUs" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/BierIiqYFUs/maxresdefault.jpg" alt="Video 2"
                            class="w-full h-32 object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('BierIiqYFUs') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=PPjkJVMRbTo" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/PPjkJVMRbTo/maxresdefault.jpg" alt="Video 3"
                            class="w-full h-32 object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('PPjkJVMRbTo') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=ujtgqHUcTQg" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/ujtgqHUcTQg/maxresdefault.jpg" alt="Video 4"
                            class="w-full h-32 object-cover rounded" />
                    </div>
                    <span class="mt-2">{{ getYoutubeTitle('ujtgqHUcTQg') }}</span>
                </a>
                <a href="https://www.youtube.com/watch?v=lxXF6Y4fT1c" target="_blank"
                    class="bg-white rounded-lg shadow p-2 flex flex-col items-center hover:shadow-lg transition-shadow">
                    <div class="relative w-full">
                        <img src="https://img.youtube.com/vi/lxXF6Y4fT1c/maxresdefault.jpg" alt="Video 5"
                            class="w-full h-32 object-cover rounded" />
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

    <section class="bg-[#03442C] py-8">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
            <div class="text-white text-2xl mb-4 md:mb-0">উত্তরাধিকার সম্পর্কিত বিষয়ের কোনো প্রশ্ন আছে?</div>
            <a href="#"
                class="text-white text-2xl mb-4 md:mb-0 underline hover:text-[#03442C] transition-colors duration-200">যোগাযোগ
                করুন ></a>
        </div>
    </section>
@endsection
