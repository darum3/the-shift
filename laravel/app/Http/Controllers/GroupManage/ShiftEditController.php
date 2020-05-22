<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\Shift;
use App\Eloquents\ShiftFixedDate;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftEditController extends Controller
{
    public function view(Request $request)
    {
        $date = FALSE;
        if ($request->has('date')) {
            $date = strtotime($request->date);
        }
        if ($date === FALSE) {
            // 直近の未入力日付表示
            $fixedList = ShiftFixedDate::whereGroupId(session('group_id'))->after(today()->tomorrow()->toImmutable())->orderBy('date_fixed')->limit(30)->get()->keyBy('date_fixed');
            for($date = today()->tomorrow(); $date <= today()->addDay(30); $date->addDay()) {
                if (is_null($fixedList->get($date->toDateString()))) {
                    break;
                }
            }
        } else {
            $date = new Carbon($request->date);
        }

        return view('g-mng.shift.view', compact('date'));
    }
}
