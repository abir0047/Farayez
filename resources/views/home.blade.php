@extends('layouts.app')

@section('content')
    <div class="relative w-full"
        style="height:300px; background: url('/hero-bg.png') center center / cover no-repeat; color: white;">
        <div class="absolute inset-0 w-full h-full" style="background:rgba(0,0,0,0.6);"></div>
        <div class="container mx-auto h-full flex flex-col justify-between relative z-10">
            <div class="pt-10 flex flex-col gap-8" style="max-width:450px;">
                <h2 class="text-2xl md:text-3xl">মুসলিম উত্তরাধিকার আইন অনুসরণী সম্পত্তি বণ্টন করুন মুহূর্তেই!</h2>
                <a href="{{ route('calculator') }}"
                    class="text-white px-6 py-3 text- border-0 rounded-sm self-start btn-green">ক্যালকুলেট
                    করুন</a>
            </div>
            <div class="flex flex-row mx-auto" style="border-radius:0; max-width:620px; width:100%; height:72px;">
                <a href="#"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 font-bold text-center"
                    style="border-radius:0; border-right:0.2px solid #e5e7eb69;">উত্তরাধিকার আইন</a>
                <a href="#"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 font-bold text-center"
                    style="border-radius:0; border-right:0.5px solid #e5e7eb69;">মুসলিম ফারায়েজ</a>
                <a href="#"
                    class="flex-1 flex items-center justify-center btn-light-green text-white px-6 py-3 font-bold text-center"
                    style="border-radius:0;">ইসলামিক উসূল</a>
            </div>
        </div>
    </div>

    <div class="bg-[#005E00] py-8 text-white">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-bold mb-2">উত্তরাধিকার আইন</h3>
                <p>জাতীয় ও আন্তর্জাতিক মুসলিম উত্তরাধিকার আইন, অ্যাংলো-মুহাম্মাদান ল, মুসলিম পারিবারিক আইন, মুসলিম
                    পারিবারিক অধ্যাদেশ ইত্যাদি।</p>
            </div>
            <div>
                <h3 class="font-bold mb-2">Muslim Family Laws Ordinance (MFLO)</h3>
                <p>পাকিস্তানের প্রেক্ষাপটে মুসলিম পরিবার আইন অধ্যাদেশ ১৯৬১ সালের মুসলিম উত্তরাধিকার আইন।</p>
            </div>
            <div>
                <h3 class="font-bold mb-2">MFLO, 1961</h3>
                <p>১৯৬১ সালের মুসলিম পরিবার আইন অধ্যাদেশ ১৯৬১ সালের মুসলিম উত্তরাধিকার আইন।</p>
            </div>
        </div>
    </div>

    <div class="bg-[#F8FAFC] py-8">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <h2 class="text-xl font-bold">মুসলিম ফারায়েজ ইতিহাস</h2>
                <span class="border-b border-[#41AB5D] w-24 md:w-48"></span>
                <h2 class="text-xl font-bold">বর্তমান বিষয়</h2>
            </div>
            <div class="mb-8">
                <p>মুসলিম ফারায়েজ ও উত্তরাধিকার আইন ইসলামের একটি গুরুত্বপূর্ণ সামাজিক ও ধর্মীয় বিধান...</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <blockquote class="text-center text-lg font-semibold text-[#005E00]">"আল্লাহ তোমাদের সন্তানদের সম্পর্কে
                    তোমাদের নির্দেশ দেন: পুরুষের ভাগ দুই নারীর সমান..." <br> (সূরা আন-নিসা, 4:11)</blockquote>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6 flex items-center justify-center font-bold text-[#005E00]">
                    সম্প্রতিক ব্লগ</div>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-[#41AB5D] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
                    <div class="bg-[#41AB5D] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
                    <div class="bg-[#41AB5D] rounded-lg shadow p-6 text-white font-bold">সম্প্রতিক ব্লগ</div>
                </div>
            </div>
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">ভিডিও</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col items-center">
                        <img src="/video-thumb.jpg" alt="Video" class="w-full h-32 object-cover rounded" />
                        <span class="mt-2">ভিডিও ১</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col items-center">
                        <img src="/video-thumb.jpg" alt="Video" class="w-full h-32 object-cover rounded" />
                        <span class="mt-2">ভিডিও ২</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col items-center">
                        <img src="/video-thumb.jpg" alt="Video" class="w-full h-32 object-cover rounded" />
                        <span class="mt-2">ভিডিও ৩</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col items-center">
                        <img src="/video-thumb.jpg" alt="Video" class="w-full h-32 object-cover rounded" />
                        <span class="mt-2">ভিডিও ৪</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-2 flex flex-col items-center">
                        <img src="/video-thumb.jpg" alt="Video" class="w-full h-32 object-cover rounded" />
                        <span class="mt-2">ভিডিও ৫</span>
                    </div>
                </div>
            </div>
            <div class="bg-[#41AB5D] rounded-lg shadow p-6 flex flex-col md:flex-row items-center justify-between">
                <div class="text-white font-bold text-lg mb-4 md:mb-0">উত্তরাধিকার সম্পর্কিত বিষয়ের কোনো প্রশ্ন আছে?</div>
                <form class="flex flex-col md:flex-row items-center gap-2">
                    <input type="email" placeholder="Your email address"
                        class="px-4 py-2 rounded-lg border border-gray-300" />
                    <button type="submit" class="bg-[#005E00] text-white px-6 py-2 rounded-lg font-bold">জিজ্ঞাসা
                        করুন</button>
                </form>
            </div>
        </div>
    </div>
@endsection
