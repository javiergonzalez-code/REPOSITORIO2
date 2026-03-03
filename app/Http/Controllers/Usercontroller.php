<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    /**
     * Devuelve los roles que el usuario actual tiene permitido gestionar.
     */
    private function getRolesPermitidos()
    {
        $user = auth()->user();

        if ($user->role === 'superadmin' || $user->email === 'admin@ragon.com') {
            return ['superadmin', 'admin', 'proveedor'];
        }

        return ['admin', 'proveedor'];
    }

    public function index()
    {
        // La tabla interactiva y los filtros de seguridad 
        // ahora están manejados por el componente Livewire
        return view('users.index');
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
            'role'     => ['required', Rule::in($rolesPermitidos)],
        ]);

        $user = User::create([
            'name'     => $validatedData['name'],
            'id'       => $validatedData['id'],
            'rfc'      => $validatedData['rfc'],
            'email'    => $validatedData['email'],
            'telefono' => $validatedData['telefono'],
            'password' => Hash::make($validatedData['password']),
            'role'     => $validatedData['role'],
        ]);

        $user->assignRole($validatedData['role']);

        Alert::success('¡Usuario Creado!', 'El usuario ha sido registrado exitosamente en el sistema.');
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $rolesPermitidos = $this->getRolesPermitidos();
        if ($user->role === 'superadmin' && !in_array('superadmin', $rolesPermitidos)) {
            Alert::error('Acceso Denegado', 'No tienes permisos para editar a un Superusuario.');
            return redirect()->route('users.index');
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
        $user->syncRoles([$validatedData['role']]);

        Alert::success('¡Actualización Exitosa!', 'Los datos del usuario han sido modificados correctamente.');
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            Alert::error('Operación Denegada', 'No puedes revocar tu propio acceso del sistema.');
            return back();
        }

        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            Alert::error('Acceso Denegado', 'No tienes permisos para eliminar a un Superusuario.');
            return back();
        }

        $user->delete();
        
        Alert::success('¡Acceso Revocado!', 'El usuario ha sido eliminado correctamente del sistema.');
        return redirect()->route('users.index');
    }
}