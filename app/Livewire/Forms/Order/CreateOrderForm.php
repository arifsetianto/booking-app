<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CreateOrderForm extends Form
{
    #[Validate('required|string|email')]
    public string $email;

    #[Validate('required|string')]
    public string $name;

    #[Validate('nullable|string|max:30')]
    public ?string $phone;

    #[Validate('nullable|string|max:100')]
    public ?string $instagram;

    #[Validate('required|uuid|exists:sources,id')]
    public string $source;

    #[Validate('nullable|string')]
    public ?string $comment;

    #[Validate('required|string')]
    public string $receiverEnName;

    #[Validate('required|string')]
    public string $receiverThName;

    #[Validate('required|uuid|exists:designations,id')]
    public string $designation;

    #[Validate('required|in:male,female')]
    public string $gender;

    #[Validate('required|uuid|exists:religions,id')]
    public string $religion;

    #[Validate('required|image|mimes:jpg,jpeg,png|max:2048')]
    public TemporaryUploadedFile $identityFile;
}
