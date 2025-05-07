<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    /**
     * Effettua il login e restituisce un token.
     *
     * @group Authentication
     * @bodyParam email string required Indirizzo email dell'utente. Example: user@example.com
     * @bodyParam password string required Password dell'utente. Example: secret
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Mario Rossi",
     *     "email": "user@example.com"
     *   },
     *   "token": "plain-text-token"
     * }
     * @response 401 {
     *   "message": "Credenziali non valide."
     * }
     * @response 422 {
     *   "errors": {
     *     "email": ["Email e password sono obbligatorie."]
     *   }
     * }
     */
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
     * Restituisce i dati dell’utente autenticato.
     *
     * @group Authentication
     * @authenticated
     * @response 200 {
     *   "id": 1,
     *   "name": "Mario Rossi",
     *   "email": "user@example.com"
     * }
     */
    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    /**
     * Revoca il token corrente.
     *
     * @group Authentication
     * @authenticated
     * @response 204 - No Content
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
