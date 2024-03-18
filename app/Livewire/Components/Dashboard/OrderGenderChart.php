<?php

declare(strict_types=1);

namespace App\Livewire\Components\Dashboard;

use App\Models\Order;
use App\ValueObject\OrderStatus;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class OrderGenderChart extends Component
{
    public array $colors = [
        'Male'   => '#2986cc',
        'Female' => '#c90076',
    ];

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $prefix = \DB::getTablePrefix();
        $orders = Order::select('order_items.gender', \DB::raw(sprintf('count(%sorder_items.id) as total', $prefix)))
                       ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                       ->whereIn(
                           'orders.status',
                           [OrderStatus::VERIFIED, OrderStatus::COMPLETED]
                       )
                       ->groupBy('order_items.gender')
                       ->get();

        $pieChartModel = $orders->reduce(
            function ($pieChartModel, $data) {
                $type = ucfirst($data->gender);
                $value = $data->total;

                return $pieChartModel->addSlice($type, $value, $this->colors[$type]);
            },
            LivewireCharts::pieChartModel()
                          ->setAnimated(true)
                          ->setType('pie')
                          ->legendPositionBottom()
                          ->legendHorizontallyAlignedCenter()
                          ->setDataLabelsEnabled(true)
        );

        return view('livewire.pages.dashboard.order-gender-chart')
            ->with(
                [
                    'pieChartModel' => $pieChartModel,
                ]
            );
    }
}
