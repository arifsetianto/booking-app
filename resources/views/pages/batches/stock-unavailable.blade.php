<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Unavailable') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-12 py-6 text-gray-900">
                    <div class="flex justify-center my-6">
                        <img src="{{ asset('images/out-of-stock.png') }}" class="w-32 h-32"/>
                    </div>
                    <p class="text-center font-semibold text-red-700 my-6">
                        Oops, we're out of stock. InsyaAllah you can order in the next batch.<br/>In meanwhile, please access our FREE Online <a href="https://thaiquran.com" target="_blank" class="underline">ThaiQuran</a> / ขอเรียนแจ้งให้ทราบว่า อัลกุรอานวากัฟแปลไทยหมดแล้วค่ะ ท่านสามารถศึกษาและเรียนรู้คัมภีร์อัลกุรอานออนไลน์ได้ทางเว็บไซต์ของเรา.
                    </p>
                    <p class="text-center font-semibold text-red-700 my-6">
                        To stay updated and be the first to know when the next batch opens, make sure to follow us on Instagram at @thaiquran or Facebook: ThaiQuran,  Don’t miss out on the opportunity to receive your copy! / ติดตามข่าวสารอัปเดตการแจกอัลกุรอานวากัฟครั้งต่อไปได้ใน IG @thaiquran หรือ FB : ThaiQuran ไทยกุรอาน.
                    </p>
                    <p class="text-center font-semibold text-red-700 my-6">
                        Thank you for your understanding and support. / ขอขอบคุณพี่น้องทุกท่านที่ให้การสนับสนุนโครงการนี้เป็นอย่างดี.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
