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
class OrderCityChart extends Component
{
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $prefix = \DB::getTablePrefix();
        $orders = Order::select('cities.en_name', \DB::raw(sprintf('count(%sorders.id) as total', $prefix)))
                       ->join('shippings', 'orders.id', '=', 'shippings.order_id')
                       ->join('sub_districts', 'shippings.sub_district_id', '=', 'sub_districts.id')
                       ->join('districts', 'sub_districts.district_id', '=', 'districts.id')
                       ->join('cities', 'districts.city_id', '=', 'cities.id')
                       ->whereIn(
                           'orders.status',
                           [OrderStatus::VERIFIED, OrderStatus::COMPLETED]
                       )
                       ->groupBy('cities.en_name')
                       ->get();

        $columnChartModel = $orders->reduce(
            function ($columnChartModel, $data) {
                $type = $data->en_name;
                $value = $data->total;

                return $columnChartModel->addColumn($type, $value, []);
            },
            LivewireCharts::columnChartModel()
                          ->setAnimated(true)
                          ->setLegendVisibility(false)
                          ->setDataLabelsEnabled(false)
                          ->setColors(['#005b96'])
                          ->setColumnWidth(90)
                          ->withGrid()
        );

        return view('livewire.pages.dashboard.order-city-chart')
            ->with(
                [
                    'columnChartModel' => $columnChartModel,
                ]
            );
    }
}
