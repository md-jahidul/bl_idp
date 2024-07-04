<?php

namespace App\Models;

use App\Models\Passport\Client;
use Illuminate\Database\Eloquent\Model;

class ClientScope extends Client
{
    public function scopes()
    {
        return $this->belongsToMany(Scope::class, 'client_scope', 'client_user_id', 'user_id');
    }
}
