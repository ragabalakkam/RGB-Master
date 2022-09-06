<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# Requests
use App\Http\Requests\Auth\CheckUniqueUsernameRequest;
use App\Http\Requests\Auth\CheckUniqueEmailRequest;
use App\Http\Requests\Auth\CheckExistsUsernameRequest;
use App\Http\Requests\Auth\CheckUniquePhoneRequest;

class CheckController extends Controller
{
    public function checkUniqueUsername(CheckUniqueUsernameRequest $request)
    {
        return response()->json(null, 200);
    }

    public function checkUniqueEmail(CheckUniqueEmailRequest $request)
    {
        return response()->json(null, 200);
    }

    public function checkUniquePhone(CheckUniquePhoneRequest $request)
    {
        return response()->json(null, 200);
    }

    public function checkExistsUsername(CheckExistsUsernameRequest $request)
    {
        return response()->json(null, 200);
    }
}
