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

    public string $search = '';

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.pages.customers.customer-grid')
            ->with(['customers' => User::select('users.*')
                                       ->leftJoin('profiles', 'users.profile_id', '=', 'profiles.id')
                                       ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                       ->join('roles', 'roles.id', '=', 'role_user.role_id')
                                       ->where('roles.name', 'customer')
                                       ->when($this->search !== '', function (Builder $query) {
                                            $query
                                                ->where(function ($qb) {
                                                    $qb
                                                        ->where('users.email', 'LIKE', '%' . $this->search . '%')
                                                        ->orWhere('users.name', 'LIKE', '%' . $this->search . '%')
                                                        ->orWhere('profiles.phone', 'LIKE', '%' . $this->search . '%')
                                                        ->orWhere('profiles.instagram', 'LIKE', '%' . $this->search . '%')
                                                    ;
                                                });
                                        })
                                       ->with('profile')
                                       ->orderByDesc('created_at')
                                       ->paginate(5)]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
