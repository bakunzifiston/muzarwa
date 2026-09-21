<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSessions
{
    /**
     * Drop this user's stored sessions so other tabs and devices stop working.
     * Only the database session driver keeps sessions we can reach from here.
     */
    public static function flush(User $user, ?string $keepSessionId = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $user->getAuthIdentifier())
            ->when($keepSessionId !== null, fn ($query) => $query->where('id', '!=', $keepSessionId))
            ->delete();
    }
}
