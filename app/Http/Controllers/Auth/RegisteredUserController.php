<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Show the general registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle general registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,restaurant_owner,recipient'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        // Create user with specified role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Show restaurant registration form.
     */
    public function showRestaurantForm()
    {
        return view('auth.register-restaurant');
    }

    /**
     * Handle restaurant registration request.
     */
    public function registerRestaurant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^\+60\d{9}$/'],
            'restaurant_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'min:10'],
            'business_license' => ['required', 'string', 'max:50'],
            'cuisine_type' => ['required', 'string', 'in:Malaysian,Chinese,Indian,Western,Italian,Japanese,Thai,International,Other'],
            'terms' => ['required', 'accepted'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('register.restaurant')
                ->withErrors($validator)
                ->withInput();
        }

        // Create restaurant owner user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'restaurant_owner',
            'status' => 'pending',
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'restaurant_name' => $request->input('restaurant_name'),
            'business_license' => $request->input('business_license'),
            'cuisine_type' => $request->input('cuisine_type'),
        ]);

        Auth::login($user);

        return redirect()->route('restaurant.dashboard');
    }

    /**
     * Show recipient registration form.
     */
    public function showRecipientForm()
    {
        return view('auth.register-recipient');
    }

    /**
     * Handle recipient registration request.
     */
    public function registerRecipient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^\+60\d{9}$/'],
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'min:10'],
            'ngo_registration' => ['required', 'string', 'max:50'],
            'recipient_capacity' => ['required', 'integer', 'min:1'],
            'organization_description' => ['required', 'string', 'min:50'],
            'terms' => ['required', 'accepted'],
        ]);

        // Handle dietary requirements (array field)
        if ($request->has('dietary_requirements')) {
            $validator->sometimes('dietary_requirements', 'array|min:1', function ($input) {
                return !empty($input->dietary_requirements);
            });
        }

        if ($validator->fails()) {
            return redirect()->route('register.recipient')
                ->withErrors($validator)
                ->withInput();
        }

        // Create recipient user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'recipient',
            'status' => 'active',
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'organization_name' => $request->input('organization_name'),
            'contact_person' => $request->input('contact_person'),
            'ngo_registration' => $request->input('ngo_registration'),
            'recipient_capacity' => $request->input('recipient_capacity'),
            'organization_description' => $request->input('organization_description'),
            'dietary_requirements' => $request->input('dietary_requirements') ? json_encode($request->input('dietary_requirements')) : null,
            'needs_preferences' => $request->input('needs_preferences'),
        ]);

        Auth::login($user);

        return redirect()->route('recipient.dashboard');
    }
}