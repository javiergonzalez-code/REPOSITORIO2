<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    private function getRolesPermitidos()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin') || $user->role === 'superadmin') {
            return ['superadmin', 'admin', 'proveedor'];
        }

        return ['admin', 'proveedor'];
    }

    public function index()
    {
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
            'rfc'      => ['nullable', 'string', 'max:13', Rule::unique('users')],
            'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in($rolesPermitidos)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'rfc.max' => 'El RFC no puede tener más de 13 caracteres.',
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // 1. Creamos la instancia sin el rol
                $user = new User([
                    'name'     => $validatedData['name'],
                    'rfc'      => $validatedData['rfc'] ?? null,
                    'email'    => $validatedData['email'],
                    'telefono' => $validatedData['telefono'] ?? null,
                    'password' => Hash::make($validatedData['password']),
                ]);

                // 2. Forzamos la asignación del rol manualmente
                $user->role = $validatedData['role'];
                $user->save();

                // 3. Asignamos en Spatie
                $user->assignRole($validatedData['role']);

                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'accion'  => 'CARGA - Registró con éxito al usuario: ' . $user->name,
                    'modulo'  => 'USUARIOS',
                ]);
            });

            Alert::success('¡Usuario Creado!', 'El usuario ha sido registrado exitosamente en el sistema.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'accion'  => Str::limit('ERROR DE REGISTRO - Falló al crear usuario: ' . $e->getMessage(), 250),
                    'modulo'  => 'USUARIOS',
                ]);
            } catch (\Exception $logError) {
                Log::error('Fallo al guardar log de creación de usuario: ' . $logError->getMessage());
            }

            Alert::error('Error', 'Ocurrió un error al registrar los datos. Verifica que la información sea correcta.');
            return back()->withInput();
        }
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

        // 🚨 1. CORRECCIÓN DE SEGURIDAD: Evitar que editen al superadmin si no tienen el rol
        if ($user->role === 'superadmin' && !in_array('superadmin', $rolesPermitidos)) {
            Alert::error('Acceso Denegado', 'No tienes permisos para modificar a un Superusuario.');
            return redirect()->route('users.index');
        }

        // ⚠️ 2. CORRECCIÓN DE PAPELERA: Agregar ->whereNull('deleted_at') al email y rfc
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'rfc'      => ['nullable', 'string', 'max:13', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
            'role'     => ['required', Rule::in($rolesPermitidos)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'rfc.max' => 'El RFC no puede tener más de 13 caracteres.',
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $validatedData) {
                // 1. Llenamos los datos ignorando explícitamente password y role
                $user->fill($request->except(['password', 'role']));

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                // 2. Forzamos la asignación del rol manualmente
                $user->role = $validatedData['role'];
                $user->save();

                // 3. Sincronizamos en Spatie
                $user->syncRoles([$validatedData['role']]);

                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'accion'  => 'ACTUALIZACIÓN - Modificó los datos del usuario: ' . $user->name,
                    'modulo'  => 'USUARIOS',
                ]);
            });

            Alert::success('¡Actualización Exitosa!', 'Los datos del usuario han sido modificados correctamente.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'accion'  => Str::limit('ERROR ACTUALIZACIÓN - ' . $e->getMessage(), 200),
                    'modulo'  => 'USUARIOS',
                ]);
            } catch (\Exception $logError) {
                Log::error('Fallo al guardar log de actualización de usuario: ' . $logError->getMessage());
            }

            Alert::error('Revisa la información', 'Parece que algunos datos son demasiado largos o tienen un formato incorrecto (ej. el RFC).');
            return back()->withInput();
        }
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

        try {
            $nombreOriginal = $user->name;

            $user->delete();

            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'ELIMINACION - Revocó acceso del usuario: ' . $nombreOriginal,
                'modulo'  => 'USUARIOS',
            ]);

            Alert::success('¡Acceso Revocado!', 'El usuario ha sido eliminado correctamente del sistema.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'accion'  => Str::limit('ERROR ELIMINACIÓN - Falló al eliminar usuario ' . $user->name . ': ' . $e->getMessage(), 250),
                    'modulo'  => 'USUARIOS',
                ]);
            } catch (\Exception $logError) {
                Log::error('Fallo al guardar log de eliminación de usuario: ' . $logError->getMessage());
            }

            Alert::error('Error', 'Ocurrió un error al procesar la solicitud.');
            return back()->withInput();
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
