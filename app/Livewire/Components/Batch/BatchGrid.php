<?php

declare(strict_types=1);

namespace App\Livewire\Components\Batch;

use App\Models\Batch;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class BatchGrid extends Component
{
    use WithPagination;

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.batches.batch-grid')->with([
            'batches' => Batch::orderBy('number')->paginate(10),
        ]);
    }
}
