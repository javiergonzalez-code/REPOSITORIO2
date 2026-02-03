<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // DEFINIR LOS ROLES
    private $roles = ['Administrador', 'Proveedor'];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%");
        })
        ->orderBy('name', 'asc') 
        ->paginate(5)
        ->withQueryString(); // Mantiene la búsqueda al cambiar de página

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
            'name'     => ['required', 'string', 'max:255'],
            'id'   => ['required', 'string', 'unique:users'],
            'rfc'      => ['nullable', 'string', 'max:13', 'unique:users'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in($this->roles)],
        ]);

        User::create([
            'name'     => $validatedData['name'],
            'id'   => $validatedData['id'],
            'rfc'      => $validatedData['rfc'],
            'email'    => $validatedData['email'],
            'telefono' => $validatedData['telefono'],
            'password' => Hash::make($validatedData['password']),
            'role'     => $validatedData['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = $this->roles;
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'rfc'      => ['nullable', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in($this->roles)],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        $user->fill($request->except('password'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Operación no permitida: No puedes eliminar tu propio acceso.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}