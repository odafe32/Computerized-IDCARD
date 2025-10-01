<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\LoginNotification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLogin()
    {
        $viewData = [
           'meta_title'=> 'Login | Teranium Co ',
           'meta_desc'=> 'A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.',
           'meta_image'=> url('logo.png'),
        ];

        return view('auth.login', $viewData);
    }

}
