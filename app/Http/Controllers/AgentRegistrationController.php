<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Organization;
use App\Models\User;
use App\Models\OrganizationPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;

class AgentRegistrationController extends Controller
{
    public function showForm(string $token = null)
    {
        $agent = null;
        if ($token) {
            $agent = Agent::where('referral_token', $token)
                ->where('is_active', true)
                ->where('status', 'approved')
                ->first();
        }
        return view('public.org-register', compact('agent'));
    }

    public function submit(Request $request, string $token = null)
    {
        $agent = null;
        if ($token) {
            $agent = Agent::where('referral_token', $token)
                ->where('is_active', true)
                ->where('status', 'approved')
                ->first();
        }

        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_phone' => 'required|string|max:20',
            'org_district' => 'nullable|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email',
            'user_password' => ['required', 'confirmed', Password::defaults()],
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Organization (Including Brand Life fields)
            $organization = Organization::create([
                'name' => $validated['org_name'],
                'phone' => $validated['org_phone'],
                'org_district' => $validated['org_district'], // Map to your correct DB column
                'tagline' => $validated['tagline'],
                'description' => $validated['description'],
                'email' => $validated['email'] ?? $validated['user_email'], // Fallback to user email
                'contact_email' => $validated['contact_email'] ?? $validated['user_email'],
                
                'is_active' => true,
                'agent_id' => $agent?->id,
                'registered_via_agent_at' => $agent ? now() : null,
                'registration_source' => $agent ? 'agent' : 'direct',
                'agent_commission_packages_count' => 0,
                'agent_commission_packages_limit' => 3,
            ]);

            // 2. Create Primary User
            $user = User::create([
                'name' => $validated['user_name'],
                'email' => $validated['user_email'],
                'password' => Hash::make($validated['user_password']),
                'organization_id' => $organization->id,
            ]);

            $user->assignRole('org_admin');
            OrganizationPackage::createFreeTrialPackage($organization->id);

            DB::commit();

            event(new Registered($user));
            Auth::guard('web')->login($user, true);
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Welcome to VENTIQ! Please check your email to verify your account.',
                'redirect' => route('filament.admin.auth.email-verification.prompt'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}