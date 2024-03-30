<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CreateOrderFromInvitationForm extends Form
{
    #[Validate('required|string|no_hyphen|max:255')]
    public ?string $name;

    #[Validate('required|string|no_hyphen|max:30')]
    public ?string $phone;

    #[Validate('nullable|string|max:100')]
    public ?string $instagram;

    #[Validate('required|uuid|exists:sources,id')]
    public ?string $source;

    #[Validate('required|string|no_hyphen')]
    public ?string $receiverEnName;

    #[Validate('required|string|no_hyphen')]
    public ?string $receiverThName;

    #[Validate('required|uuid|exists:designations,id')]
    public ?string $designation;

    #[Validate('required|in:male,female')]
    public ?string $gender;

    #[Validate('required|uuid|exists:religions,id')]
    public ?string $religion;

    #[Validate(
        'nullable|image|mimes:jpg,jpeg,png|max:5120'
        , message: [
        'max' => 'Maximum upload file size is 5MB'
    ])]
    public ?TemporaryUploadedFile $identityFile = null;

    #[Validate('nullable|string')]
    public ?string $comment = null;

    #[Validate('required|numeric|max_digits:30')]
    public ?string $receiverPhone;

    #[Validate('required|string|no_hyphen')]
    public ?string $address;

    #[Validate('required|uuid|exists:regions,id')]
    public ?string $region = '';

    #[Validate('required|uuid|exists:cities,id')]
    public ?string $city = '';

    #[Validate('required|uuid|exists:districts,id')]
    public ?string $district = '';

    #[Validate('required|uuid|exists:sub_districts,id')]
    public ?string $subDistrict = '';

    #[Validate('required|numeric|min:0')]
    public int $fee;

    #[Validate('required|string')]
    public ?string $zipCode;

    #[Validate('required|image|mimes:jpg,jpeg,png|max:5120', message: ['max' => 'Maximum upload file size is 5MB'])]
    public TemporaryUploadedFile $receiptFile;
}
