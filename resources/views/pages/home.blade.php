<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            <div class="overflow-hidden">
                <div class="p-6 text-gray-900">
                    <div class="alert alert-success flex items-center justify-between bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded">
                        <div class="flex items-center">
                <span class="text-sm">
                    ✅ Registration Successful!
<br/>
Good news! 🎉 Your registration has been successfully completed, and your account is now approved for login.
<br/><br/>
🚨 No Email Verification Needed – Due to an issue with our email server (Mailgun services down), we have skipped the email verification step to ensure a smooth experience.

No need to worry! You can now fully access your account and continue using ThaiQuran services as usual.

Thank you for your patience and understanding!
                    <hr class="my-6 border-green-200 sm:mx-auto dark:border-green-700 lg:my-6" />
                    ✅ ลงทะเบียนสำเร็จแล้ว!
<br/>
🎉 ยินดีด้วย! การลงทะเบียนของคุณเสร็จสมบูรณ์แล้ว และบัญชีของคุณ ได้รับการอนุมัติสำหรับการเข้าสู่ระบบ
<br/><br/>
🚨 ไม่ต้องยืนยันอีเมล – เนื่องจากปัญหาเกี่ยวกับเซิร์ฟเวอร์อีเมล (Mailgun ขัดข้อง), เราได้ ข้ามขั้นตอนการยืนยันอีเมล เพื่อให้คุณสามารถใช้งานได้อย่างราบรื่น

ไม่ต้องกังวล! คุณสามารถเข้าสู่ระบบและใช้งาน ThaiQuran ได้ตามปกติแล้ว

ขอขอบคุณที่เข้าใจและสนับสนุนเรา
                </span>
                        </div>
                    </div>
                    <livewire:pages.products.list-products />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
