<?php

use App\Models\Batch;
use App\ValueObject\BatchStatus;
use Carbon\Carbon;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {
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

        return sprintf('%02d:%02d:%02d:%02d', $days, $hours, $minutes, $seconds);
    }
};

?>

<div>
    <div
        class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <a href="{{ route('orders.book') }}">
            <img class="pb-8 rounded-t-lg" src="{{ asset('images/product-1.jpg') }}" alt="product image"/>
        </a>
        <div class="px-5 pb-5">
            <a href="{{ route('orders.book') }}">
                <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Thai-translation
                    Quran</h5>
            </a>
            <div class="flex items-center mt-2.5 mb-5">
                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                    <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor" viewBox="0 0 22 20">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor" viewBox="0 0 22 20">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor" viewBox="0 0 22 20">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor" viewBox="0 0 22 20">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                    <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="currentColor" viewBox="0 0 22 20">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                    </svg>
                </div>
                <span
                    class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 ms-3">5.0</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Free</p>
                    @if($pendingBatch)
                        <p class="text-sm font-normal text-gray-900 dark:text-white">Available
                            Stock {{ number_format(num: $pendingBatch->getAvailableStock(), thousands_separator: '.') }}</p>
                    @elseif($publishedBatch && $publishedBatch->getAvailableStock() > 0)
                        <p class="text-sm font-normal text-gray-900 dark:text-white">Available
                            Stock {{ number_format(num: $publishedBatch->getAvailableStock(), thousands_separator: '.') }}</p>
                    @else
                        <p class="text-sm font-semibold text-red-700 dark:text-white">Out of stock</p>
                    @endif
                </div>
                @if($pendingBatch)
                    @if($countdownTime)
                        <a href="{{ route('orders.book') }}"
                           class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 pointer-events-none opacity-50 cursor-not-allowed">
                            <span class="flex items-center justify-between">
                                <span class="mr-2 whitespace-nowrap text-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" data-tooltip-target="tooltip-lock">
                                          <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                <span class="text-sm font-semibold text-gray-100 dark:text-white">{{ $this->formatTime($countdownTime) }}</span>
                            </span>
                        </a>
                    @endif
                @elseif($publishedBatch && $publishedBatch->getAvailableStock() > 0)
                    <a href="{{ route('orders.book') }}"
                       class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Book
                        now!</a>
                @else
                    <a href="{{ route('orders.book') }}"
                       class="text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 pointer-events-none opacity-50 cursor-not-allowed">Book
                        now!</a>
                @endif
            </div>
        </div>
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
