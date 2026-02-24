<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Devuelve los roles que el usuario actual tiene permitido gestionar.
     */
    private function getRolesPermitidos()
    {
        $user = auth()->user();

        // El Superadmin ve todo
        if ($user->role === 'superadmin' || $user->email === 'admin@ragon.com') {
            return ['superadmin', 'admin', 'proveedor'];
        }

        // El Admin normal solo ve roles inferiores
        return ['admin', 'proveedor'];
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $rolesPermitidos = $this->getRolesPermitidos();

        $query = User::query();

        // 1. Restricción de visibilidad: Un admin NO superadmin no puede ver a los superadmins en la lista
        if (!in_array('superadmin', $rolesPermitidos)) {
            $query->where('role', '!=', 'superadmin');
        }

        // 2. Buscador general
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%");
            });
        }

        // 3. Filtro por nombre
        if ($request->filled('user')) {
            $query->where('name', 'like', '%' . $request->user . '%');
        }

        // 4. Filtro por Rol (Validando que el rol filtrado esté entre sus permitidos)
        if ($request->filled('role') && in_array($roleFilter, $rolesPermitidos)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('name', 'asc')
            ->paginate(5)
            ->withQueryString();

        $usuarios_filtro = User::select('name')->orderBy('name', 'asc')->get();
        
        // Pasamos los roles filtrados a la vista para el dropdown del filtro
        $roles = $rolesPermitidos;

        return view('users.index', compact('users', 'search', 'usuarios_filtro', 'roles'));
    }

    public function create()
    {
        $roles = $this->getRolesPermitidos();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rolesPermitidos = $this->getRolesPermitidos();

        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'id'       => ['required', 'string', 'unique:users'],
            'rfc'      => ['nullable', 'string', 'max:13', 'unique:users'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in($rolesPermitidos)], // Seguridad: Solo roles permitidos
        ]);

        $user = User::create([
            'name'     => $validatedData['name'],
            'id'       => $validatedData['id'],
            'rfc'      => $validatedData['rfc'],
            'email'    => $validatedData['email'],
            'telefono' => $validatedData['telefono'],
            'password' => Hash::make($validatedData['password']),
            'role'     => $validatedData['role'], // MySQL
        ]);

        // Sincronizar con Spatie (Backpack)
        $user->assignRole($validatedData['role']);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        // Seguridad: Si un admin intenta editar a un superadmin por URL directa
        $rolesPermitidos = $this->getRolesPermitidos();
        if ($user->role === 'superadmin' && !in_array('superadmin', $rolesPermitidos)) {
            abort(403, 'No tienes permisos para editar a un Superusuario.');
        }

        $roles = $rolesPermitidos;
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rolesPermitidos = $this->getRolesPermitidos();

        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'rfc'      => ['nullable', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in($rolesPermitidos)],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        $user->fill($request->except('password'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Actualizar el rol en Spatie/Backpack también
        $user->syncRoles([$validatedData['role']]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Operación no permitida: No puedes eliminar tu propio acceso.');
        }

        // Seguridad: No permitir que un admin normal borre a un superadmin
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}