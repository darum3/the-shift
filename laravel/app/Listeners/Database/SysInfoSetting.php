<?php

namespace App\Listeners\Database;

use App\Events\CommonSaving;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SysInfoSetting
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommonSaving  $event
     * @return void
     */
    public function handle(CommonSaving $event)
    {
        $model = $event->model;

        $trace = debug_backtrace();
        for($i = 2; $i < count($trace); ++$i) {
            if (isset($trace[$i]['class']) && $trace[$i]['class'] === 'Illuminate\Database\Eloquent\Model' && $trace[$i]['function'] === 'save') {
                break;
            }
        }
        $class = isset($trace[$i + 1]) ? $trace[$i + 1]['class'] : 'Unknown';

        $model->sysinfo = $class;
    }
}
