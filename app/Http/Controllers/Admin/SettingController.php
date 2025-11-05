<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorageGlobalSetting;
use App\Models\BannedExtension;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\StorageGroupLimit;
use App\Models\User;
use App\Models\StorageUserLimit;

class SettingController extends Controller
{
    public function index()
    {
        $global = StorageGlobalSetting::first();
        $banned = BannedExtension::orderBy('extension')->get();

        return view('admin.settings.index', compact('global', 'banned'));
    }

    public function updateGlobal(Request $request)
    {
        $data = $request->validate([
            'default_quota_mb' => ['required','integer','min:1','max:20480'], // hasta 20 GB
        ]);

        $row = StorageGlobalSetting::first();
        if (!$row) {
            $row = new StorageGlobalSetting();
        }
        $row->default_quota_mb = $data['default_quota_mb'];
        $row->save();

        return redirect()->route('admin.settings.index')->with('ok', 'Cuota global actualizada.');
    }

    public function addBanned(Request $request)
    {
        $data = $request->validate([
            'extension' => ['required','string','max:20'],
        ]);

        $ext = strtolower(ltrim($data['extension'], '.')); // normaliza (sin punto)
        BannedExtension::firstOrCreate(['extension' => $ext]);

        return redirect()->route('admin.settings.index')->with('ok', "Extensión .$ext agregada.");
    }

    public function deleteBanned(BannedExtension $bannedExtension)
    {
        $ext = $bannedExtension->extension;
        $bannedExtension->delete();

        return redirect()->route('admin.settings.index')->with('ok', "Extensión .$ext eliminada.");
    }
    public function groupLimits()
    {
        $groups = Group::orderBy('name')->get();
        // join simple para ver las cuotas actuales
        $limits = StorageGroupLimit::with('group')->get();
        return view('admin.settings.group-limits', compact('groups','limits'));
    }

    public function saveGroupLimit(Request $request)
    {
        $data = $request->validate([
            'group_id' => ['required','exists:groups,id'],
            'quota_mb' => ['nullable','integer','min:1','max:20480'], // null = hereda global
        ]);

        StorageGroupLimit::updateOrCreate(
            ['group_id' => $data['group_id']],
            ['quota_mb' => $data['quota_mb']]
        );

        return redirect()->route('admin.settings.group.limits')->with('ok', 'Cuota de grupo guardada.');
    }

    public function deleteGroupLimit(StorageGroupLimit $groupLimit)
    {
        $groupLimit->delete();
        return redirect()->route('admin.settings.group.limits')->with('ok', 'Cuota de grupo eliminada (vuelve a heredar la global).');
    }
    public function userLimits()
    {
        // listamos usuarios básicos (id, name, email) y límites existentes
        $users  = User::orderBy('name')->get(['id','name','email']);
        $limits = StorageUserLimit::with('user')->get();
        return view('admin.settings.user-limits', compact('users','limits'));
    }

    public function saveUserLimit(Request $request)
    {
        $data = $request->validate([
            'user_id'  => ['required','exists:users,id'],
            'quota_mb' => ['nullable','integer','min:1','max:20480'], // null = hereda grupo/global
        ]);

        StorageUserLimit::updateOrCreate(
            ['user_id' => $data['user_id']],
            ['quota_mb' => $data['quota_mb']]
        );

        return redirect()->route('admin.settings.user.limits')->with('ok', 'Cuota de usuario guardada.');
    }

    public function deleteUserLimit(StorageUserLimit $userLimit)
    {
        $userLimit->delete();
        return redirect()->route('admin.settings.user.limits')->with('ok', 'Cuota de usuario eliminada (hereda grupo/global).');
    }
}
