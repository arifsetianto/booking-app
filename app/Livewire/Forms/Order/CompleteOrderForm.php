<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CompleteOrderForm extends Form
{
    #[Validate('required|string|no_hyphen|unique:shippings,tracking_code')]
    public string $trackingCode = '';
}
