<?php
#app/Http/Admin/Controllers/AdminCustomerConfigController.php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminInOuts;
use Illuminate\Http\Request;

class AdminInOutsController extends Controller
{
    public function index(Request $request)
    {
        $data = AdminInOuts::all();
        $inOut = AdminInOuts::find($request->id);
        $total = AdminInOuts::sum('amount');
        return view('admin.screen.in_outs')->with(compact('data','inOut','total'));
    }

    public function save(Request $request){
        $inOut = AdminInOuts::updateOrCreate(['id'=>$request->id], $request->all());
        return redirect(route('in_outs.index'));
    }

    public function delete(Request $request){
        AdminInOuts::destroy($request->id);
        return redirect(route('in_outs.index'));
    }

}
