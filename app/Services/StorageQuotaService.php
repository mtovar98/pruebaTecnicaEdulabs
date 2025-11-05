<?php

namespace App\Services;

use App\Models\StorageGlobalSetting;
use App\Models\StorageGroupLimit;
use App\Models\StorageUserLimit;
use App\Models\FileItem;
use App\Models\User;

class StorageQuotaService
{
    /**
     * Retorna la cuota asignada al usuario EN BYTES,
     * siguiendo la prioridad: User > (mínimo de sus Grupos) > Global.
     */
    public function assignedQuotaBytes(User $user): int
    {
        // 1) User-specific
        $userLimit = StorageUserLimit::where('user_id', $user->id)->value('quota_mb');
        if (!is_null($userLimit)) {
            return (int) $userLimit * 1024 * 1024;
        }

        // 2) Group-specific (si pertenece a varios grupos, tomamos el más restrictivo: MÍNIMO no nulo)
        $groupIds = $user->groups()->pluck('groups.id');
        if ($groupIds->isNotEmpty()) {
            $groupMin = StorageGroupLimit::whereIn('group_id', $groupIds)->min('quota_mb');
            if (!is_null($groupMin)) {
                return (int) $groupMin * 1024 * 1024;
            }
        }

        // 3) Global
        $globalMb = StorageGlobalSetting::query()->value('default_quota_mb') ?? 10;
        return (int) $globalMb * 1024 * 1024;
    }

    /**
     * Retorna el uso actual (en bytes) del usuario sumando sus FileItem.
     */
    public function currentUsageBytes(User $user): int
    {
        return (int) FileItem::where('user_id', $user->id)->sum('size_bytes');
    }
}
