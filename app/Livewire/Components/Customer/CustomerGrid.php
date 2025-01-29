<?php

declare(strict_types=1);

namespace App\Livewire\Components\Customer;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class CustomerGrid extends Component
{
    use WithPagination;

    public string $searchKeyword = '';

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.customers.customer-grid')
            ->with(['customers' => User::select('users.*')
                                       ->leftJoin('profiles', 'users.profile_id', '=', 'profiles.id')
                                       ->when($this->searchKeyword !== '', function (Builder $query) {
                                            $query
                                                ->where(function ($qb) {
                                                    $qb
                                                        ->where('users.email', 'LIKE', '%' . $this->searchKeyword . '%')
                                                        ->orWhere('users.name', 'LIKE', '%' . $this->searchKeyword . '%')
                                                        ->orWhere('profiles.phone', 'LIKE', '%' . $this->searchKeyword . '%')
                                                        ->orWhere('profiles.instagram', 'LIKE', '%' . $this->searchKeyword . '%')
                                                    ;
                                                });
                                        })
                                       ->with('profile')
                                       ->orderByDesc('created_at')
                                       ->paginate(10)]);
    }
}
