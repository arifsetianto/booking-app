<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class HeaderlessLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.headerless');
    }
}
