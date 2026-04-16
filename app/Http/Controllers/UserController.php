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

        // Se mapean las validaciones únicas a las columnas de SAP. 
        // Se elimina la lógica de la papelera de reciclaje (Soft Deletes).
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'rfc'      => ['nullable', 'string', 'max:13', Rule::unique('users', 'LicTradNum')],
            'email'    => ['required', 'email', Rule::unique('users', 'E_Mail')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in($rolesPermitidos)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'rfc.max' => 'El RFC no puede tener más de 13 caracteres.',
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // Mapeo exacto de los inputs del formulario a las columnas de SAP Business One
                $user = new User([
                    'CardName'   => $validatedData['name'],
                    'LicTradNum' => $validatedData['rfc'] ?? null,
                    'E_Mail'     => $validatedData['email'],
                    'Cellular'   => $validatedData['telefono'] ?? null,
                    'password'   => Hash::make($validatedData['password']),
                ]);

                $user->role = $validatedData['role'];
                $user->save();

                $user->assignRole($validatedData['role']);

                \App\Models\Log::create([
                    'user_id' => auth()->user()->CardCode, 
                    'accion'  => 'CARGA - Registró con éxito al usuario: ' . $user->CardName, 
                    'modulo'  => 'USUARIOS',
                ]);
            });

            Alert::success('¡Usuario Creado!', 'El usuario ha sido registrado exitosamente en el sistema.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->user()->CardCode, 
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

    public function edit($id) 
    {
        $user = User::where('CardCode', $id)->firstOrFail();
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id) 
    {
        $user = User::where('CardCode', $id)->firstOrFail();
        $rolesPermitidos = $this->getRolesPermitidos();

        if ($user->role === 'superadmin' && !in_array('superadmin', $rolesPermitidos)) {
            Alert::error('Acceso Denegado', 'No tienes permisos para modificar a un Superusuario.');
            return redirect()->route('users.index');
        }

        // Corrección de validación unique para las columnas nativas, excluyendo la papelera
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'rfc'      => ['nullable', 'string', 'max:13', Rule::unique('users', 'LicTradNum')->ignore($user->CardCode, 'CardCode')],
            'email'    => ['required', 'email', Rule::unique('users', 'E_Mail')->ignore($user->CardCode, 'CardCode')],
            'role'     => ['required', Rule::in($rolesPermitidos)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'rfc.max' => 'El RFC no puede tener más de 13 caracteres.',
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $validatedData) {
                // Asignación directa en lugar de fill() para sincronizar los nombres dispares (input vs bd)
                $user->CardName = $validatedData['name'];
                $user->LicTradNum = $request->input('rfc');
                $user->E_Mail = $validatedData['email'];
                $user->Cellular = $request->input('telefono');

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->role = $validatedData['role'];
                $user->save();

                $user->syncRoles([$validatedData['role']]);

                \App\Models\Log::create([
                    'user_id' => auth()->user()->CardCode,
                    'accion'  => 'ACTUALIZACIÓN - Modificó los datos del usuario: ' . $user->CardName, 
                    'modulo'  => 'USUARIOS',
                ]);
            });

            Alert::success('¡Actualización Exitosa!', 'Los datos del usuario han sido modificados correctamente.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->user()->CardCode,
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

    public function destroy($id)
    {
        $user = User::where('CardCode', $id)->firstOrFail();

        if (auth()->user()->CardCode === $user->CardCode) {
            Alert::error('Operación Denegada', 'No puedes revocar tu propio acceso del sistema.');
            return back();
        }

        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            Alert::error('Acceso Denegado', 'No tienes permisos para eliminar a un Superusuario.');
            return back();
        }

        try {
            $nombreOriginal = $user->CardName; 
            
            // Eliminación permanente directa
            $user->delete();

            \App\Models\Log::create([
                'user_id' => auth()->user()->CardCode, 
                'accion'  => 'ELIMINACION - Revocó acceso del usuario: ' . $nombreOriginal,
                'modulo'  => 'USUARIOS',
            ]);

            Alert::success('¡Acceso Revocado!', 'El usuario ha sido eliminado correctamente del sistema.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            try {
                \App\Models\Log::create([
                    'user_id' => auth()->user()->CardCode,
                    'accion'  => Str::limit('ERROR ELIMINACIÓN - Falló al eliminar usuario ' . $user->CardName . ': ' . $e->getMessage(), 250),
                    'modulo'  => 'USUARIOS',
                ]);
            } catch (\Exception $logError) {
                Log::error('Fallo al guardar log de eliminación de usuario: ' . $logError->getMessage());
            }

            Alert::error('Error', 'Ocurrió un error al procesar la solicitud.');
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $user = User::where('CardCode', $id)->firstOrFail();
        return view('users.show', compact('user'));
    }
}