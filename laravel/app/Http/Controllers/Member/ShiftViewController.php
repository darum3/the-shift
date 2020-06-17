<?php

namespace App\Http\Controllers\Member;

use App\Eloquents\Shift;
use App\Eloquents\ShiftFixedDate;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Eloquents\Group;

class ShiftViewController extends Controller
{
    public function list(Request $request)
    {
        $group = Group::find(session('group_id'));
        $this->authorize('view', $group);

        $year = $request->year;
        $month = $request->month;

        if (is_null($year) || is_null($month)) {
            $carbon = today()->startOfMonth();
        } else {
            $carbon = now()->startOfDay()->startOfMonth()->year($year)->month($month);
            if ($carbon < today()->startOfDay()->startOfMonth()) {
                return redirect(url()->current());
            }
        }

        $fixed = ShiftFixedDate::month($carbon->toImmutable())->whereGroupId(session('group_id'))->get()->keyBy('date_fixed');
        $shifts = Shift::month(session('group_id'), Auth::user()->id, $carbon->toImmutable())
            ->with('work_type')->orderBy('start_datetime')->get();
        $shifts = $shifts->filter(function($shift, $k) use($fixed) {
            return !is_null($fixed->get($shift->start_datetime->toDateString()));
        });

        return view('member.shift.list', compact('carbon', 'shifts'));
    }

    public function view(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $group = Group::find(session('group_id'));
        $this->authorize('view', $group);

        $date = CarbonImmutable::parse($request->date);

        return view('member.shift.view', compact('date'));
    }
}
