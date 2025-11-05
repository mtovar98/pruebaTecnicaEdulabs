<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;

class UserController extends Controller
{
    public function index()
    {
        // usuarios con sus grupos
        $users = User::with('groups:id,name')->orderBy('name')->get(['id','name','email']);
        $groups = Group::orderBy('name')->get(['id','name']); // lo usaremos en el próximo paso
        return view('admin.users.index', compact('users','groups'));
    }
    public function assignGroup(Request $request, User $user)
    {
        $data = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        // evita duplicados
        $user->groups()->syncWithoutDetaching([$data['group_id']]);

        return back()->with('ok', 'Grupo asignado.');
    }

    public function removeGroup(User $user, Group $group)
    {
        $user->groups()->detach($group->id);
        return back()->with('ok', 'Grupo quitado.');
    }
}
