<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRules;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'estado' => 'activo',
            'email_verified_at' => now()
        ]);

        $user->assignRole('usuario');

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles')
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            $user = Auth::user();
            $roles = $user->roles->pluck('name');
            $permissions = $user->permissions->pluck('name');

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Sesión cerrada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            $roles = $user->roles->pluck('name');
            $permissions = $user->permissions->pluck('name');

            return response()->json([
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener información del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'email|unique:users,email,' . $user->id,
                'password' => ['nullable', 'confirmed', PasswordRules::defaults()],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'message' => 'Perfil actualizado exitosamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => 'Enlace de recuperación enviado exitosamente']);
            }

            return response()->json(['message' => 'No se pudo enviar el enlace de recuperación'], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Contraseña actualizada exitosamente']);
            }

            return response()->json(['message' => 'No se pudo actualizar la contraseña'], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restablecer la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al refrescar el token',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function getRoles()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            return response()->json([
                'roles' => $user->roles,
                'permissions' => $user->permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllUsers()
    {
        try {
            $users = User::with('roles')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'email|unique:users,email,' . $user->id,
                'password' => ['nullable', 'confirmed', PasswordRules::defaults()],
                'estado' => 'in:activo,inactivo'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('estado')) {
                $user->estado = $request->estado;
            }

            $user->save();

            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignRole(Request $request, User $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,name'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user->syncRoles($request->roles);

            return response()->json([
                'message' => 'Roles asignados exitosamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al asignar roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
