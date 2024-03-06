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
    #[Validate('required|image|mimes:jpg,jpeg,png|max:2048')]
    public TemporaryUploadedFile $receiptFile;
}
