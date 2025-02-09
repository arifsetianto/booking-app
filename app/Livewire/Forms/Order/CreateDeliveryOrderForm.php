<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Order;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CreateDeliveryOrderForm extends Form
{
    #[Validate('required|string|no_hyphen')]
    public string $name;

    #[Validate('required|numeric|max_digits:30')]
    public string $phone;

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

    #[Validate('required|numeric')]
    public float $fee;

    #[Validate('required|string')]
    public string $zipCode;
}
