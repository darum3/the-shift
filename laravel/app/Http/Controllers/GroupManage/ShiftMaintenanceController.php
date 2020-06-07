<?php

namespace App\Http\Controllers\GroupManage;

use App\Eloquents\OffHour;
use App\Eloquents\Shift;
use App\Eloquents\WorkType;
use App\Http\Controllers\Controller;
use App\Rules\ExistsIdArray;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftMaintenanceController extends Controller
{
    /*
    Sample
    [{
        "date": "YYYY-mm-dd",
        "shift": [{
            "user_id": xxx,
            "work_type_code": "XXX", // コード
            "startTime": hhmm,
            "endTime": hhmm,
            off_hours: [{
                "startTime": hhmm,
                "endTime": hhmm,
            }, ...] // 休憩時間 なければ指定なし(Optional)
        }, ...] // 人数分
    }, ...]
    */
    public function insert(Request $request)
    {
        DB::transaction(function () use($request) {
            $groupId = session('group_id');
            $workTypes = WorkType::whereContractId(session('contract_id'))->get()->keyBy('code');

            foreach($request->all() as $i => $element) {
                if(!isset($element["date"])) {
                    abort(400, sprintf("Date field missing: Element #%d", $i+1));
                }

                $date = new CarbonImmutable($element["date"]);
                $presents = Shift::date($date)->whereGroupId($groupId)->get()->groupBy('user_id');

                foreach($element["shift"] as $j => $shift) {
                    $user_id = $shift['user_id'] ?? null;
                    if (!isset($user_id)) {
                        abort(400, 'user_id missing: '.$date->toDateString().' Element #'.($j + 1));
                    }
                    $shifts = $presents->get($user_id);
                    if(!is_null($shifts)) {
                        // 既存データ削除
                        Shift::destroy($shifts->pluck('id'));
                        $presents->forget($user_id); // コレクションから削除
                    }

                    // validation
                    $workType = $workTypes->get($shift['work_type_code']);
                    if (is_null($workType)) {
                        abort(400, sprintf("work_type_code INVALID: %s Element #%d", $date->toDateString(), $j+1));
                    }

                    // Insert
                    $start = $this->hhmmToArray($shift['startTime']);
                    $end = $this->hhmmToArray($shift['endTime']);
                    $endDateTime = new Carbon($date);
                    if ($shift['endTime'] <= $shift['startTime']) {
                        $endDateTime->addDay();
                    }
                    $endDateTime->hour($end['hour'])->minute($end['minute']);
                    // データなし: Insert
                    $newEntity = Shift::create([
                        'group_id' => $groupId,
                        'user_id' => $user_id,
                        'work_type_id' => $workType->id,
                        'start_datetime' => Carbon::create($date->year, $date->month, $date->day, $start['hour'], $start['minute']),
                        'end_datetime' => $endDateTime,
                    ]);

                    // 休憩
                    if (isset($shift['off_hours'])) {
                        foreach($shift['off_hours'] as $offHour) {
                            $start = $this->hhmmToArray($offHour['startTime']);
                            $end = $this->hhmmToArray($offHour['endTime']);

                            $startDate = new Carbon($date);
                            if ($offHour['startTime'] <= $shift['startTime']) {
                                $startDate->addDay();
                            }
                            $startDate->hour($start['hour'])->minute($start['minute']);
                            $endDate = new Carbon($date);
                            if ($offHour['endTime'] <= $shift['startTime']) {
                                $endDate->addDay();
                            }
                            $endDate->hour($end['hour'])->minute($end['minute']);
                            OffHour::create([
                                'shift_id' => $newEntity->id,
                                'off_start_datetime' => $startDate,
                                'off_end_datetime' => $endDate,
                            ]);
                        }
                    }
                }
            }
        });

        return ['Result' => "OK"];
    }

    protected function hhmmToArray(string $hhmm) : ?array
    {
        if (strlen($hhmm) !== 4) {
            return null;
        }

        $ret['hour'] = substr($hhmm, 0, 2);
        $ret['minute'] = substr($hhmm, 2);
        return $ret;
    }

    public function get(String $date)
    {
        if (strtotime($date) === FALSE) {
            abort(400);
        }

        $carbon = CarbonImmutable::parse($date);
        $shifts = Shift::whereGroupId(session('group_id'))->date($carbon)
        ->with('user', 'work_type', 'off_hour')->get()
        ->groupBy('user_id');
        $respShifts = [];
        foreach($shifts as $userShift) {
            foreach($userShift as $shift) {
                $respShifts[] = [
                    "user_id" => $shift->user_id,
                    "name" => $shift->user->name,
                    "task" => [
                        "task_id" => $shift->id,
                        "startTime" => Carbon::parse($shift->start_datetime)->format('Hi'),
                        "endTime" => Carbon::parse($shift->end_datetime)->format('Hi'),
                        "work_type" => $shift->work_type->category,
                    ],
                ];
            }
        }

        return [
            [
                'date' => $carbon->toDateString(),
                'shifts' => $respShifts,
            ],
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'task_id' => ['required', new ExistsIdArray($request, Shift::class)],
        ]);

        DB::transaction(function () use($request) {
            Shift::whereIn('id', $request->task_id)->delete();
        });

        return ['Result' => 'OK'];
    }
}
