<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ForceUpdateOrderForm extends UpdateOrderForm
{
    #[Validate('required|string')]
    public string $receiverName;

    #[Validate('required|string|max:30')]
    public string $receiverPhone;

    #[Validate('required|string')]
    public string $address;

    #[Validate('required|uuid|exists:regions,id')]
    public string $region = '';

    #[Validate('required|uuid|exists:cities,id')]
    public string $city = '';

    #[Validate('required|uuid|exists:districts,id')]
    public string $district = '';

    #[Validate('required|uuid|exists:sub_districts,id')]
    public string $subDistrict = '';

    #[Validate('required|numeric|min:0')]
    public int $fee;

    #[Validate('nullable|image|mimes:jpg,jpeg,png|max:2048')]
    public ?TemporaryUploadedFile $receiptFile = null;
}
