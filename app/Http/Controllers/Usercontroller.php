<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // DEFINIR LOS ROLES
    private $roles = ['Administrador', 'Proveedor'];
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderby('name')
            ->paginate(15)
            ->withQueryString();
        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = $this->roles;
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in($this->roles)],
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Colaborador registrado correctamente.');
    }
    public function edit(User $user)
    {
        $roles = $this->roles;
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in($this->roles)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill($request->except('password'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
                         ->with('success', 'Datos del colaborador actualizados.');
    }

    public function destroy(User $user)
    {
        // Seguridad: No permitir que el usuario actual se borre a sí mismo
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Operación no permitida: No puedes eliminar tu propio acceso.');
        }

        $user->delete();
        return redirect()->route('users.index')
                         ->with('success', 'Acceso revocado y usuario eliminado.');
    }
}
