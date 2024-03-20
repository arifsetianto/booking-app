<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ImportOrderForm extends Form
{
    #[Validate('required|file|mimes:xlsx')]
    public TemporaryUploadedFile $importFile;
}
