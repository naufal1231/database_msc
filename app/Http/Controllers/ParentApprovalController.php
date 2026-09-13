<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParentApprovalController extends Controller
{
    /**
     * Approve the pending parent account.
     */
    public function approve(User $user)
    {
        if ($user->role !== 'user' || $user->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permohonan akun pending yang dapat disetujui.');
        }

        $user->update([
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', "Akun orang tua {$user->name} berhasil disetujui.");
    }

    /**
     * Reject/Delete the pending parent account.
     */
    public function reject(User $user)
    {
        if ($user->role !== 'user' || $user->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya permohonan akun pending yang dapat ditolak.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Pendaftaran akun orang tua {$name} telah ditolak dan dihapus.");
    }
}
