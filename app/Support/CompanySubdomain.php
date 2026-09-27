<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CompanySubdomain
{
    private const RESERVED = [
        'admin', 'api', 'app', 'assets', 'blog', 'cdn', 'contacto', 'correo',
        'gestion', 'info', 'mail', 'panel', 'soporte', 'status', 'www',
    ];

    public function isAvailable(string $slug, ?int $exceptApplicationId = null): bool
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/', $slug)
            || in_array($slug, self::RESERVED, true)) {
            return false;
        }

        $query = DB::table('company_applications')
            ->where(function ($query) use ($slug): void {
                $query->where('reserved_slug', $slug)->orWhere('approved_slug', $slug);
            });

        if ($exceptApplicationId !== null) {
            $query->where('id', '!=', $exceptApplicationId);
        }

        return ! $query->exists();
    }
}
