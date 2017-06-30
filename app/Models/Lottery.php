<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    protected $table = 'lottery';

    protected $primaryKey = 'lotteryid';

    protected $fillable = [
        'cnname',
        'enname',
        'sorts',
        'lotterytype',
        'issueset',
        'weekcycle',
        'yearlybreakstart',
        'yearlybreakend',
        'mincommissiongap',
        'minprofit',
        'issuerule',
        'description',
        'numberrule',
        'retry',
        'delay',
        'pushtime',
        'lock_insert_set',
        'replace_set',
        'our_rate_set',
        'our_rate_set_max',
        'sale_amt_set',
        'prize_amt_set',
        'our_safe_rate_set',
        'unlocked',
        'country',
    ];

    public $timestamps = false;

    public function getIssuesetAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    public function setIssuesetAttribute($value)
    {
        $this->attributes['issueset'] = Arr::accessible($value) ? serialize($value) : [];
    }

    public function getNumberruleAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    public function setNumberruleAttribute($value)
    {
        $this->attributes['numberrule'] = Arr::accessible($value) ? serialize($value) : [];
    }
}