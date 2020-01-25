<?php
namespace App\Eloquents;

use App\Events\CommonSaving;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model {
    use SoftDeletes;

    protected $dispatchesEvents = [
        'saving' => CommonSaving::class,
    ];

    protected function myWhere($query, $column, $param)
    {
        $method = is_array($param) ? 'whereIn' : 'where';
        return $query->{$method}($column, $param);
    }
}
