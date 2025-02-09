<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new #[Layout('layouts.guest')] class extends Component {
    public ?Batch $publishedBatch = null;
    public ?Batch $pendingBatch = null;

    public int $countdownTime = 0;

    public function mount(): void
    {
        $this->publishedBatch = Batch::where('status', BatchStatus::PUBLISHED)->first();
        $this->pendingBatch = Batch::where('status', BatchStatus::PENDING)->orderBy('publish_at')->first();

        if ($this->pendingBatch) {
            $this->countdownTime = Carbon::now()->diffInSeconds($this->pendingBatch->publish_at);
        }
    }

    public function decrementCountdown(): void
    {
        if ($this->countdownTime > 0) {
            $this->countdownTime--;
        } else {
            $this->dispatch('refresh-page');
        }
    }

    public function formatTime($seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d : %02d : %02d : %02d', $days, $hours, $minutes, $seconds);
    }

    public function agree(): void
    {
        //$this->redirect(url: '/login-email', navigate: true);
        $this->redirect(url: '/login-guest', navigate: true);
    }
};

?>

<div>
    <div class="sm:p-2 md:p-4">
        <h5 class="mb-4 text-2xl font-extrabold text-center leading-none tracking-tight text-blue-950 md:text-3xl lg:text-4xl dark:text-gray-400">
            Order your FREE<br/><span class="text-blue-950 dark:text-blue-500">Thai<span
                    class="text-gold-400">Quran</span></span> today!</h5>
        <div class="flex flex-col justify-center py-5 items-center text-gray-900 dark:text-white">
            @if($pendingBatch)
                <span class="text-xl font-semibold mb-2 text-blue-950 dark:text-gray-400">Available Stock</span>
                <span
                    class="text-5xl font-extrabold tracking-tight text-blue-950">{{ number_format(num: $pendingBatch->getAvailableStock(), thousands_separator: '.') }}</span>
                @if($countdownTime > 0)
                    <span
                        class="text-lg font-semibold tracking-tight text-blue-950 mt-4 mb-2 text-center">Booking Open at<br><span
                            class="font-bold">{{ $pendingBatch->publish_at->format('l, j F Y \a\t g.iA') }}</span></span>
                    <span class="text-lg font-semibold tracking-tight text-blue-950 mt-2 mb-4 text-center">Begins In<br>
                    <span id="countdown" class="text-3xl text-red-700 font-extrabold">{{ $this->formatTime($countdownTime) }}</span></span>
                @endif
            @elseif($publishedBatch && $publishedBatch->getAvailableStock() >= 1)
                <span class="text-xl font-semibold mb-2 text-blue-950 dark:text-gray-400">Stock Available Now!</span>
                <span
                    class="text-5xl font-extrabold tracking-tight text-blue-950">{{ number_format(num: $publishedBatch->getAvailableStock(), thousands_separator: '.') }}</span>
            @else
                <span class="text-lg font-semibold mb-2 text-red-700 dark:text-gray-400">Oops, we're out of stock. InsyaAllah you can order in the next batch. In meanwhile, please access our FREE Online <a
                        href="https://thaiquran.com" target="_blank" class="underline">ThaiQuran</a></span>
            @endif
        </div>
        <div>
            <p class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400">
                ข้อตกลงและเงื่อนไขของการขอรับอัลกุรอานวากัฟ / Terms and Conditions for Requesting Quran Waqf</p>
        </div>
        <ul role="list" class="space-y-3 mt-4 mb-7">
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">จัดส่งเฉพาะในประเทศไทยเท่านั้น / Delivery only in Thailand</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">ขอสงวนสิทธิ์อัลกุรอานแปลไทยให้สำหรับคนไทยที่มีบัตรประจำตัวประชาชนเท่านั้น / The Thai translation of the Quran is reserved for Thai people with national ID cards only</span>
            </li>
            <li class="flex items-start">
                <svg class="flex-shrink-0 w-4 h-4 mt-0.5 text-blue-950 dark:text-blue-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="text-base font-normal leading-tight text-gray-500 dark:text-gray-400 ms-3">กรุณาตรวจสอบให้แน่ใจว่าคุณได้สร้างบัญชีส่วนตัวในเว็บไซต์ก่อนลงทะเบียนขอรับอัลกุรอานเพื่อหลีกเลี่ยงเว็บไซต์ล่มในวันลงทะเบียน / Make sure you have a registered account at thaiquran before you can make a booking (<a
                        href="{{ route('register') }}" class="text-blue-950 font-semibold">ลงทะเบียน / Register Now</a>)</span>
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
        {{--        @if($batch && $batch->getAvailableStock() >= 1)--}}
        <button type="button" wire:click="agree"
                class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center">
            ฉันได้อ่าน​ และยอมรับ​
        </button>
        {{--        @else--}}
        {{--            <button type="button"--}}
        {{--                    class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-200 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex justify-center w-full text-center opacity-50 cursor-not-allowed" disabled>--}}
        {{--                ฉันได้อ่าน​ และยอมรับ​--}}
        {{--            </button>--}}
        {{--        @endif--}}
    </div>
</div>

@if($countdownTime > 0)
    <script>
        setInterval(function () {
        @this.call('decrementCountdown');
        }, 1000);
    </script>
@endif

<script>
    window.addEventListener('refresh-page', () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    });
</script>
