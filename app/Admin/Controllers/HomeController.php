<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminMenu;
use App\Http\Controllers\Controller;
use App\Models\ShopNews;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopCountry;
use App\Models\ShopUser;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        AdminMenu::getListVisible();
        $data = [];
        $data['title'] = trans('admin.dashboard');
        $data['users'] = new ShopUser;
        $data['orders'] = new ShopOrder;
        $data['mapStyleStatus'] = ShopOrder::$mapStyleStatus;
        $data['products'] = new ShopProduct;
        $data['blogs'] = new ShopNews;

        //Country statistics
        // $dataCountries = (new ShopOrder)->getCountryInYear();
        // $arrCountry = ShopCountry::getArray();
        // $arrCountryMap   = [];
        // $ctTotal = 0;
        // $ctTop = 0;
        // foreach ($dataCountries as $key => $country) {
        //     $ctTotal +=$country->count;
        //     if($key <= 3) {
        //         $ctTop +=$country->count;
        //         if($key == 0) {
        //             $arrCountryMap[] =  [
        //                 'name' => $arrCountry[$country->country],
        //                 'y' => $country->count,
        //                 'sliced' => true,
        //                 'selected' => true,
        //             ];
        //         } else {
        //             $arrCountryMap[] =  [$arrCountry[$country->country], $country->count];
        //         }
        //     }
        // }
        // $arrCountryMap[] = ['Other', ($ctTotal - $ctTop)];
        // $data['dataPie'] = json_encode($arrCountryMap);
        //End country statistics


        //Order in 30 days
        $totalsInMonth = (new ShopOrder)->getSumOrderTotalInMonth("2020-05-01","2020-05-31")->keyBy('md')->toArray();
        $rangDays = new \DatePeriod(
            new \DateTime('-1 month'),
            new \DateInterval('P1D'),
            new \DateTime('+1 day')
        );
        $orderInMonth  = [];
        $amountInMonth  = [];
        foreach ($rangDays as $i => $day) {
            $date = $day->format('m-d');
            $orderInMonth[$date] = $totalsInMonth[$date]['total_order'] ?? '';
            $amountInMonth[$date] = ($totalsInMonth[$date]['total_amount'] ?? 0);
            $costInMonth[$date] = ($totalsInMonth[$date]['total_cost'] ?? 0);
            $profitsInMonth[$date] = ($totalsInMonth[$date]['profits'] ?? 0);
        }
        $data['orderInMonth'] = $orderInMonth;
        $data['amountInMonth'] = $amountInMonth;
        $data['costInMonth'] = $costInMonth;
        $data['profitsInMonth'] = $profitsInMonth;

        //End order in 30 days
        
        //Order in 12 months
        $totalsMonth = (new ShopOrder)->getSumOrderTotalInYear()
            ->pluck('total_amount', 'ym')->toArray();
        $dataInYear = [];
        for ($i = 12; $i >= 0; $i--) {
            $date = date("Y-m", strtotime(date('Y-m-01') . " -$i months"));
            $dataInYear[$date] = $totalsMonth[$date] ?? 0;
        }
        $data['dataInYear'] = $dataInYear;
        //End order in 12 months

        return view('admin.home', $data);
    }

    public function mainChartData(Request $request){
        $from = $request->from;
        $to = $request->to;
        $data = [];
        //Order in 30 days
        $totalsInMonth = (new ShopOrder)->getSumOrderTotalInMonth($from, $to)->keyBy('md')->toArray();
        $rangDays = new \DatePeriod(
            new \DateTime($from),
            new \DateInterval('P1D'),
            new \DateTime($to)
        );
        $orderInMonth  = [];
        $amountInMonth  = [];   
        foreach ($rangDays as $i => $day) {
            $date = $day->format('d/m');
            $orderInMonth[$date] = $totalsInMonth[$date]['total_order'] ?? '';
            $amountInMonth[$date] = ($totalsInMonth[$date]['total_amount'] ?? 0);
            $costInMonth[$date] = ($totalsInMonth[$date]['total_cost'] ?? 0);
            $profitsInMonth[$date] = ($totalsInMonth[$date]['profits'] ?? 0);
        }
        $data['orderInMonth'] = $orderInMonth;
        $data['amountInMonth'] = $amountInMonth;
        $data['costInMonth'] = $costInMonth;
        $data['profitsInMonth'] = $profitsInMonth;

        return response()->json($data);
    }

    public function deny()
    {
        $data = [
            'title' => trans('admin.deny'),
            'icon' => '',
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view('admin.deny', $data);
    }
}
