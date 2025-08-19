<?php

namespace App\Models;

use App\Traits\TruncatesDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class StakingPolicy extends Model
{
    use HasFactory, TruncatesDecimals;

    protected $fillable = [
        'is_active',
        'coin_id',
        'staking_type',
        'min_quantity',
        'max_quantity',
        'daily',
        'period',
        'staking_days',
    ];

    protected $casts = [
        'staking_name' => 'array',
        'daily' => 'decimal:9',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'staking_locale_name',
        'staking_locale_memo',
    ];

    public function translations()
    {
        return $this->hasMany(StakingPolicyTranslation::class, 'policy_id', 'id');
    }

    public function getStakingLocaleNameAttribute()
    {
        return optional($this->translationForLocale())->name;
    }

    public function getStakingLocaleMemoAttribute()
    {
        return optional($this->translationForLocale())->memo;
    }

    public function translationForLocale($locale = null)
    {
        $locale = $locale ?? Cookie::get('app_locale', 'en');

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->firstWhere('locale', $locale);
    }

    public function isStakingAvailableToday()
    {
        $staking_days = explode(',', $this->staking_days ?? '');
        $today = strtolower(now()->format('D'));

        return in_array($today, $staking_days);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static $columnDescriptions = [
        'staking_name' => '상품 이름',
        'min_quantity' => '최소 참여수량',
        'max_quantity' => '최대 참여수량',
        'daily' => '데일리 수익률',
        'period' => '기간',
    ];

    public function getColumnComment($column)
    {
        return static::$columnDescriptions[$column];
    }
}
