<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class product extends Model
{
    use HasFactory;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin == 1;
    }

}