<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CurrentUser
{
    /**
     * netuqo is single-user for now — login is a deliberately later increment (see
     * ROADMAP.md). Every row is still scoped to a real user_id from day one; this just
     * resolves which one until real authentication exists.
     */
    public static function resolve(): User
    {
        return User::firstOrCreate(
            ['email' => 'owner@netuqo.com'],
            ['name' => 'Owner', 'password' => Hash::make(Str::random(40))],
        );
    }
}
