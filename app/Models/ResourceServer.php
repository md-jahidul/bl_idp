<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceServer extends Model
{
    protected $fillable = ['name', 'description'];

    public function scopes()
    {
        return $this->hasMany(Scope::class, 'resource_server', 'name');
    }
}
