<?php

namespace App\Http\Controllers\Member;

use App\Eloquents\DesiredShift;
use App\Eloquents\InputedDate;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesiredController extends Controller
{
    public function list(int $week = null)
    {
        $groupId = session('group_id');
        $userId = Auth::user()->id;
        if (is_null($week)) {
            $week = now()->week;
        }
        $from = today()->week($week)->startOfWeek()->toImmutable();
        $to = $from->endOfWeek();
        $desired = DesiredShift::whereGroupId($groupId)
            ->whereUserId($userId)
            ->range($from, $to)
            ->orderBy('date_target')
            ->orderBy('time_start')
            ->get()
            ->groupBy('date_target');
        $inputed = InputedDate::whereGroupId($groupId)
            ->whereUserId($userId)
            ->range($from, $to)
            ->get()
            ->pluck('date');
        return view('member.desired.list', compact('from', 'to', 'desired', 'inputed', 'week'));
    }

    public function edit(string $date)
    {
        if(strtotime($date) === FALSE) {
            abort(404);
        }
        $target = CarbonImmutable::parse($date);
        $sunday = $target->startOfWeek();

        return view('member.desired.edit', compact('target', 'sunday'));
    }
}
