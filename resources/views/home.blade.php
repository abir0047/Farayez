@extends('layouts.app')

@section('content')
    <section class="text-center py-8 md:py-12 bg-blue-100 px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-blue-900 mb-4 md:mb-6">
            মুসলিম উত্তরাধিকার আইন অনুযায়ী সম্পত্তি বন্টন করুন মুহূর্তেই!
        </h2>
        <a href="{{ route('calculator') }}"
            class="bg-blue-500 text-white px-4 py-2 md:px-6 md:py-2 rounded text-sm md:text-base">
            ক্যালকুলেট করুন
        </a>
    </section>

    <section class="container mx-auto px-4 py-8 md:py-12">
        <h3 class="text-xl md:text-2xl font-semibold text-center mb-4">আমাদের সেবাসমূহ</h3>
        <ul class="space-y-3 md:space-y-4 text-base md:text-lg">
            <li class="text-center md:text-left">✅ ইসলামী শরীয়াহ অনুযায়ী সম্পত্তি বন্টন</li>
            <li class="text-center md:text-left">✅ অভিজ্ঞ আইনজীবীদের সহায়তা</li>
        </ul>
    </section>

    <section class="bg-gray-100 py-8 md:py-12 px-4">
        <div class="container mx-auto text-center">
            <blockquote class="text-lg md:text-xl italic font-semibold">
                <p>“আর তোমরা নারীদেরকে তাদের মাহর (বিবাহ-প্রদানীয় অর্থ) খুশি মনে প্রদান কর...”</p>
                <cite class="block mt-2 text-blue-900 text-base md:text-lg">সূরা আন-নিসা- 4</cite>
            </blockquote>
        </div>
    </section>

    <section class="container mx-auto px-4 py-8 md:py-12">
        <h3 class="text-xl md:text-2xl font-semibold text-center mb-4 md:mb-6">আমাদের সাথে যোগাযোগ করুন</h3>
        <form class="mt-4 md:mt-6 w-full md:max-w-lg mx-auto bg-white p-4 md:p-6 shadow-md md:shadow-lg rounded">
            <input type="text" class="border p-2 w-full mb-3 md:mb-4 text-sm md:text-base" placeholder="আপনার নাম">
            <input type="email" class="border p-2 w-full mb-3 md:mb-4 text-sm md:text-base" placeholder="ইমেইল">
            <input type="text" class="border p-2 w-full mb-3 md:mb-4 text-sm md:text-base" placeholder="ফোন">
            <textarea class="border p-2 w-full mb-4 md:mb-6 text-sm md:text-base h-32" placeholder="আপনার বার্তা"></textarea>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 md:px-6 md:py-2 rounded text-sm md:text-base">
                পাঠান
            </button>
        </form>
    </section>
@endsection
