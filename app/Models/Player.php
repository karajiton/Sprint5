<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Player extends Model
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    protected $fillable = ['Nickname','Email', 'success_rate'];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
