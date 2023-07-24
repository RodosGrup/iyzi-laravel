<?php

namespace RodosGrup\IyziLaravel\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IyzicoUser extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [];

    public function cards()
    {
        return $this->hasMany(StoredCreditCard::class, 'user_key', 'user_key');
    }
}
