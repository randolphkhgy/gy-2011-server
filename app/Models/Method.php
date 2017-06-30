<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    protected $table = 'method';

    protected $fillable = [
        'pid',
        'lotteryid',
        'crowdid',
        'methodname',
        'code',
        'jscode',
        'is_special',
        'addslastype',
        'functionname',
        'functionrule',
        'initlockfunc',
        'areatype',
        'maxcodecount',
        'level',
        'nocount',
        'description',
        'isclose',
        'islock',
        'lockname',
        'maxlost',
        'totalmoney',
        'modes',
        'iscompare',
        'source_id',
        'isbroker'
    ];

    public $timestamps = false;

    public function getFunctionruleAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    public function setFunctionruleAttribute($value)
    {
        $this->attributes['functionrule'] = Arr::accessible($value) ? serialize($value) : [];
    }

    public function getNocountAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    public function setNocountAttribute($value)
    {
        $this->attributes['nocount'] = Arr::accessible($value) ? serialize($value) : [];
    }
}