<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController {
    public function checkAuthPassword(Request $request) {
        $password = $request->input('key');

        if ($password === env('AUTH_PASSWORD')) {
            return response()->json(['access' => true]);
        } else {
            return response()->json(['access' => false]);
        }
    }
}


