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
class OrderSourceChart extends Component
{
    public array $colors = [
        'Event'                            => '#f6ad55',
        'Facebook'                         => '#3b5998',
        'Family/Friend'                    => '#90cdf4',
        'Google'                           => '#008744',
        'Instagram'                        => '#d62976',
        'Masjid/Community'                 => '#ffa700',
        'Other'                            => '#673ab7',
        'Review/Testimony in Social Media' => '#008080',
        'Tiktok'                           => '#ff00e7',
    ];

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $prefix = \DB::getTablePrefix();
        $orders = Order::select('sources.name', \DB::raw(sprintf('count(%sorders.id) as total', $prefix)))
                       ->join('sources', 'orders.source_id', '=', 'sources.id')
                       ->whereIn('orders.status', [OrderStatus::VERIFIED, OrderStatus::COMPLETED])
                       ->groupBy('orders.source_id')
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

        return view('livewire.pages.dashboard.order-source-chart')
            ->with(
                [
                    'pieChartModel' => $pieChartModel,
                ]
            );
    }
}
