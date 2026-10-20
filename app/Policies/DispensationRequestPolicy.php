<?php

namespace App\Policies;

use App\Models\DispensationRequest;
use App\Models\User;

class DispensationRequestPolicy
{
    /**
     * Hanya orang tua yang bisa melihat dan mengunduh dispensasinya sendiri.
     */
    public function view(User $user, DispensationRequest $dispensation): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        return $user->id === $dispensation->parent_id;
    }

    /**
     * Orang tua membuat pengajuan baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    /**
     * Orang tua bisa memperbarui (resubmit) jika statusnya rejected.
     */
    public function update(User $user, DispensationRequest $dispensation): bool
    {
        return $user->id === $dispensation->parent_id
            && $dispensation->isRejected();
    }

    /**
     * Hanya admin/coach yang bisa menyetujui.
     */
    public function approve(User $user, DispensationRequest $dispensation): bool
    {
        return in_array($user->role, ['admin', 'super_admin'])
            && $dispensation->isPending();
    }

    /**
     * Hanya admin/coach yang bisa menolak.
     */
    public function reject(User $user, DispensationRequest $dispensation): bool
    {
        return in_array($user->role, ['admin', 'super_admin'])
            && $dispensation->isPending();
    }

    /**
     * Unduh PDF hanya jika approved dan PDF sudah ada.
     */
    public function download(User $user, DispensationRequest $dispensation): bool
    {
        if (!$dispensation->isApproved() || !$dispensation->hasPdf()) {
            return false;
        }

        // Admin bisa unduh; orang tua hanya punyanya sendiri
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        return $user->id === $dispensation->parent_id;
    }

    /**
     * Unggah template hanya untuk admin/super_admin.
     */
    public function uploadTemplate(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
