<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    const COUNTRY_CHINA = 1;
    const COUNTRY_VIETNAM = 3;

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

    /**
     * @param  object  $value
     * @return mixed|object
     */
    public function getIssuesetAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    /**
     * @param  \ArrayAccess|array|null  $value
     */
    public function setIssuesetAttribute($value)
    {
        $this->attributes['issueset'] = Arr::accessible($value) ? serialize($value) : [];
    }

    /**
     * @param  object  $value
     * @return mixed|object
     */
    public function getNumberruleAttribute($value)
    {
        return ($value) ? unserialize($value) : (object) [];
    }

    /**
     * @param  \ArrayAccess|array|null  $value
     */
    public function setNumberruleAttribute($value)
    {
        $this->attributes['numberrule'] = Arr::accessible($value) ? serialize($value) : [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function methods()
    {
        return $this->hasMany(Method::class, 'lotteryid', 'lotteryid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issueInfo()
    {
        return $this->hasMany(IssueInfo::class, 'lotteryid', 'lotteryid');
    }
}