<?php

namespace RodosGrup\IyziLaravel\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface IyzicoUser
{
    public function cards(): HasMany;
}
