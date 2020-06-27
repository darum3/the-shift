<?php

namespace App\Http\Controllers\Member;

use App\Eloquents\DesiredShift;
use App\Eloquents\InputedDate;
use App\Eloquents\WorkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\DesiredFix;
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
            ->pluck('date_target');
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
        $groupId = session('group_id');
        $userId = Auth::user()->id;
        $originalDatas = DesiredShift::whereGroupId($groupId)
            ->whereUserId($userId)
            ->whereDateTarget($date)
            ->get();

        return view('member.desired.edit', compact('target', 'sunday', 'workTypes', 'originalDatas'));
    }

    public function register(DesiredRegister $request)
    {
        DB::transaction(function () use($request) {
            $groupId = session('group_id');
            $userId = Auth::user()->id;
            foreach($request->all() as $data) {
                $targetDate = CarbonImmutable::create($data['target_date']);

                DesiredShift::where([
                    'group_id' => $groupId,
                    'user_id' => $userId,
                    'date_target' => $targetDate->toDateString(),
                ])->delete();

                foreach($data['desired'] as $desired) {
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
            }
        });

        return ['Result' => 'OK'];
    }

    public function fix(DesiredFix $request)
    {
        DB::transaction(function () use($request) {
            $groupId = session('group_id');
            $userId = Auth::user()->id;

            foreach($request->date as $date) {
                $entity = InputedDate::whereGroupId($groupId)->whereUserId($userId)->whereDateTarget($date)->first();
                if (is_null($entity)) {
                    InputedDate::create([
                        'group_id' => $groupId,
                        'user_id' => $userId,
                        'date_target' => $date,
                    ]);
                } else {
                    $entity->save();
                }
            }
        });

        session()->flash('member.desired.fix');
        return redirect()->route('member.desired');
    }
}
