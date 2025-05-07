<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // 1) validazione input
            $credentials = [
                'email'    => $request->getUser(),
                'password' => $request->getPassword(),
            ];
         
            if (empty($credentials['email']) || empty($credentials['password'])) {
                throw ValidationException::withMessages([
                    'email' => ['Email e password sono obbligatorie.'],
                ]);
            }

            // 2) tentativo di autenticazione
            if (! Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Credenziali non valide.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 3) generazione token
            $user  = $request->user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user'  => $user,
                'token' => $token,
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // input non valido: 422 Unprocessable Entity
            return response()->json([
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            // qualsiasi altro errore: 500 con messaggio in JSON
            return response()->json([
                'message' => 'Errore interno: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restituisce i dati dell’utente autenticato via Sanctum.
     */
    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    /**
     * Revoca (elimina) il token corrente.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
