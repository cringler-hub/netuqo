<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class Controller
{
    /**
     * netuqo is single-user for now — login is a deliberately later increment (see
     * ROADMAP.md). Every row is still scoped to a real user_id from day one; this just
     * resolves which one until real authentication exists.
     */
    protected function currentUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'owner@netuqo.com'],
            ['name' => 'Owner', 'password' => Hash::make(Str::random(40))],
        );
    }
}
