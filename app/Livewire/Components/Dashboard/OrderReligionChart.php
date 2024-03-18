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
class OrderReligionChart extends Component
{
    public array $colors = [
        'Mualaf' => '#f9a73e',
        'Muslim' => '#005b96',
        'Non-muslim interested in quran' => '#bf212f',
    ];

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $prefix = \DB::getTablePrefix();
        $orders = Order::select('religions.name', \DB::raw(sprintf('count(%sorder_items.id) as total', $prefix)))
                       ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                       ->join('religions', 'order_items.religion_id', '=', 'religions.id')
                       ->whereIn(
                           'orders.status',
                           [OrderStatus::VERIFIED, OrderStatus::COMPLETED]
                       )
                       ->groupBy('order_items.religion_id')
                       ->get();

        $pieChartModel = $orders->reduce(
            function ($pieChartModel, $data) {
                $type = $data->name;
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

        return view('livewire.pages.dashboard.order-religion-chart')
            ->with(
                [
                    'pieChartModel' => $pieChartModel,
                ]
            );
    }
}
