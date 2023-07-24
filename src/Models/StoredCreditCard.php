<?php

namespace RodosGrup\IyziLaravel\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredCreditCard extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [];
}
