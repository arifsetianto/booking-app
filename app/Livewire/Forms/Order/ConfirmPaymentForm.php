<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ConfirmPaymentForm extends Form
{
    #[Validate('required|image|max:5120', message: ['max' => 'Maximum upload file size is 5MB'])]
    public TemporaryUploadedFile $receiptFile;
}
