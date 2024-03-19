<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    public ?Batch $batch = null;

    public function mount(): void
    {
        $this->batch = Batch::where('status', BatchStatus::PUBLISHED)->first();
    }

    public function agree(): void
    {
        $this->redirect(url: '/login-email', navigate: true);
    }
};

?>

<div>
    <div class="sm:p-2 md:p-4">
        <h5 class="mb-4 text-2xl font-extrabold text-center leading-none tracking-tight text-blue-950 md:text-3xl lg:text-4xl dark:text-gray-400">
            Order your FREE<br/><span class="text-blue-950 dark:text-blue-500">Thai<span class="text-gold-400">Quran</span></span> today!</h5>
        <div class="flex flex-col justify-center py-5 items-center text-gray-900 dark:text-white">
            @if($batch && $batch->getAvailableStock() >= 1)
                <span class="text-xl font-semibold mb-2 text-blue-950 dark:text-gray-400">Available Stock</span>
                <span class="text-5xl font-extrabold tracking-tight text-blue-950">{{ $batch->total_stock - $batch->purchased_stock }}</span>
            @else
                <span class="text-lg font-semibold mb-2 text-red-700 dark:text-gray-400">Oops, we're out of stock. InsyaAllah you can order in the next batch. In meanwhile, please access our FREE Online <a href="https://thaiquran.com" target="_blank" class="underline">ThaiQuran</a></span>
            @endif
        </div>
        <div>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">ข้อตกลงและเงื่อนไขของการขอรับอัลกุรอานวากัฟ</p>
        </div>
        <ul role="list" class="space-y-3 mt-4 mb-7">
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">จัดส่งเฉพาะในประเทศไทยเท่านั้น</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">ขอสงวนสิทธิ์อัลกุรอานแปลไทยให้สำหรับคนไทยที่มีบัตรประจำตัวประชาชนเท่านั้น</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">⁠เราจำเป็นต้องให้สิทธิ์แก่ผู้ที่ลงทะเบียนครบถ้วนและถูกต้องก่อน​  กรุณาลงทะเบียนตามวันเวลาที่กำหนดเท่านั้นและทำตามกฎอย่างเคร่งครัดเพื่อความรวดเร็วในการตรวจสอบข้อมูล</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">
                    มูลนิธิไทยกุรอาน​ได้กำหนดนโยบาย
​การปกป้องข้อมูลส่วนบุคคล​ ​ และจัดทำ
ระบบบริหารจัดการการปกป้องข้อมูลส่วน
บุคคลขึ้นเพื่อให้แน่ใจว่าจะมีการบังคับใช้
นโยบายอย่างเคร่งครัด เรามีการดำเนินการ
อย่างต่อเนื่องเพื่อปรับปรุงและดูแลรักษาระบบ
ของเรา พร้อมการสื่อสารนโยบายการปกป้องข้อมูลส่วนบุคคลของเราไปยังบุคลากรทุกคน
ของมูลนิธิไทยกุรอาน​ เราจึงขอความยินยอม
จากท่านเพื่อจัดเก็บ​ รวบรวม​ข้อมูลส่วนตัวเพื่อ
จุดประสงค์ดังต่อไปนี้​
                    <ol class="ps-7 mt-2 space-y-1 list-decimal list-inside">
                        <li class="list-outside">เพื่อยืนยันตัวตน​และจัดส่งอัลกุรอานวากัฟแปลไทย</li>
                        <li class="list-outside">เป็นหลักฐานว่าทีมได้ทำหน้าที่ตรงตามจุดประสงค์ของมูลนิธิไทยกุรอาน​</li>
                        <li class="list-outside">เพื่อติดตาม​ ประเมินผลและทำสถิติผู้สนใจขอรับอัลกุรอาน​วากัฟ</li>
                        <li class="list-outside">เพื่อส่งข่าวสาร​หรือโครงการอื่นๆเกี่ยวกับมูลนิธิไทยกุรอานให้ผู้สนใจทางอีเมล</li>
                    </ol>
                </span>
            </li>
        </ul>
        @if($batch && $batch->getAvailableStock() >= 1)
            <button type="button" wire:click="agree"
                    class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">
                ฉันได้อ่าน​ และยอมรับ​
            </button>
        @else
            <button type="button"
                    class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center opacity-50 cursor-not-allowed" disabled>
                ฉันได้อ่าน​ และยอมรับ​
            </button>
        @endif
    </div>
</div>
