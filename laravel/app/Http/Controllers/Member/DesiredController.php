<?php

namespace App\Http\Controllers\Member;

use App\Eloquents\DesiredShift;
use App\Eloquents\InputedDate;
use App\Eloquents\WorkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\DesiredRegister;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesiredController extends Controller
{
    public function list(int $week = null)
    {
        $groupId = session('group_id');
        $userId = Auth::user()->id;
        if (is_null($week)) {
            $week = now()->week;
        }
        $from = today()->week($week)->startOfWeek(Carbon::SUNDAY)->toImmutable();
        $to = $from->endOfWeek(Carbon::SATURDAY);
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
        $sunday = $target->startOfWeek(Carbon::SUNDAY);
        $workTypes = WorkType::whereContractId(session('contract_id'))->get();

        return view('member.desired.edit', compact('target', 'sunday', 'workTypes'));
    }

    public function register(DesiredRegister $request)
    {
        DB::transaction(function () use($request) {
            $groupId = session('group_id');
            $userId = Auth::user()->id;
            $targetDate = CarbonImmutable::create($request->target_date);

            DesiredShift::where([
                'group_id' => $groupId,
                'user_id' => $userId,
                'date_target' => $targetDate->toDateString(),
            ])->delete();

            foreach($request->desired as $desired) {
                $workTypeId = WorkType::where([
                    'contract_id' => session('contract_id'),
                    'code' => $desired['work_type'],
                ])->first()->id;
                DesiredShift::create([
                    'group_id' => $groupId,
                    'user_id' => $userId,
                    'date_target' => $targetDate->toDateString(),
                    'work_type_id' => $workTypeId,
                    'time_start' => CarbonImmutable::parse($desired['start']),
                    'time_end' => CarbonImmutable::parse($desired['end']),
                ]);
            }
        });

        return ['Result' => 'OK'];
    }
}
