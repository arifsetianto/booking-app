<?php

namespace App\Livewire\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginEmailForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';
}
