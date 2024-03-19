<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class UpdateOrderForm extends Form
{
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

    #[Validate('nullable|image|mimes:jpg,jpeg,png|max:5120')]
    public ?TemporaryUploadedFile $identityFile = null;

    #[Validate('nullable|string')]
    public ?string $comment = null;
}
