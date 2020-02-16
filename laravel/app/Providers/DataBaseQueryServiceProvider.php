<?php

namespace App\Providers;

use DB;
use Log;
use Event;
use Carbon\Carbon;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\ServiceProvider;

class DataBaseQueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (!config('database.query_log')) {
            return;
        }

        DB::listen(function($query) {
            $sql = $query->sql;
            if (preg_match('/^select/i', $sql) == 1) { // SELECT文のみ値を表示
                foreach ($query->bindings as $binding) {
                    if (is_string($binding)) {
                        $binding = "'{$binding}'";
                    } elseif (is_null($binding)) {
                        $binding = 'NULL';
                    } elseif ($binding instanceof Carbon) {
                        $binding = "'{$binding->toDateTimeString()}'";
                    } elseif ($binding instanceof \DateTime) {
                        $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                    } elseif ($binding === false) {
                        $binding = 'FALSE';
                    }

                    $sql = preg_replace("/\?/", $binding, $sql, 1);
                }
            }
            Log::debug('SQL', ['sql' => $sql, 'time' => "$query->time ms", 'file' => $this->getTrace()]);
        });

        Event::listen(TransactionBeginning::class, function (TransactionBeginning $event) {
            Log::debug('START TRANSACTION', ['file' => $this->getTrace()]);
        });

        Event::listen(TransactionCommitted::class, function (TransactionCommitted $event) {
            Log::debug('COMMIT', ['file' => $this->getTrace()]);
        });

        Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event) {
            Log::debug('ROLLBACK', ['file' => $this->getTrace()]);
        });
    }

    private function getTrace()
    {
        $trace = debug_backtrace();
        for($i = 1; $i < count($trace); ++$i) {
            if (isset($trace[$i]['file']) && strpos($trace[$i]['file'], DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'laravel') === FALSE) {
                break;
            }
        }
        $filepos = isset($trace[$i]) ? $trace[$i]['file'] . ':' . $trace[$i]['line'] : 'Unknown';
        return $filepos;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
