<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class InviteOrderForm extends Form
{
    #[Validate('required|email')]
    public string $email;

    #[Validate('required|uuid|exists:batches,id')]
    public string $batch;
}
