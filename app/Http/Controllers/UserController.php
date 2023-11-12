<?php

namespace App\Http\Controllers;

use App\Models\RoleUser;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()->with("roles")->get();
        return Inertia::render('User/UserList', ['users'=> $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::query()->with("roles")->get();
        return Inertia::render('User/UserCreate', ['users'=> $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'guichet' => $request->guichet,
            'password' => Hash::make($request->password),
        ]);

        if ($request->role_id) {
            $role = new RoleUser([
                'user_id' => $user->id,
                'role_id' => $request->get("role_id"),
            ]);
            $role->save();
        }

        return redirect()->route("users.index")->with("success","Utilisateurs enregistré avec succès");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::query()->with("roles")->findOrFail($id);
        return Inertia::render('User/UserShow', ['user'=> $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::query()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($user->id)]
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->guichet = $request->guichet;

        $user->save();
        
        return redirect()->route("users.index")->with('succes', 'Enregistré avec succès!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateUserRole(Request $request)
    {
        $request->validate([
            "user_id" => "required|numeric",
            "role_id" => "required|numeric",
        ]);

        $role = new RoleUser([
            'user_id' => $request->get('user_id'),
            'role_id' => $request->get("role_id"),
        ]);
        $role->save();

        return redirect()->back()->with("success","Enregistré avec succès");
    }
}
