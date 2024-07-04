<?php

namespace App\Models;

use App\Models\Passport\Client;
use Illuminate\Database\Eloquent\Model;

class Scope extends Model
{
    protected $fillable = ['resource_server', 'scope'];

    public function oauthClients()
    {
        return $this->belongsToMany(ClientScope::class, 'client_scope', 'scope_id', 'client_user_id');
    }
}
