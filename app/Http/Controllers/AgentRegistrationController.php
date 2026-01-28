<?php
// app/Http/Controllers/AgentRegistrationController.php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AgentRegistrationController extends Controller
{
    /**
     * Show organization registration form
     */
    public function showForm(string $token)
    {
        // Find agent by referral token
        $agent = Agent::where('referral_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        return view('public.agent-registration', compact('agent'));
    }

    /**
     * Process organization registration
     */
    public function submit(Request $request, string $token)
    {
        // Validate agent token
        $agent = Agent::where('referral_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        // Validate input
        $validated = $request->validate([
            // Organization details
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|email|max:255|unique:organizations,email',
            'org_phone' => 'required|string|max:20',
            'org_description' => 'nullable|string|max:1000',
            'org_website' => 'nullable|url|max:255',
            
            // Primary user details
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email',
            'user_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Organization
            $organization = Organization::create([
                'name' => $validated['org_name'],
                'email' => $validated['org_email'],
                'phone' => $validated['org_phone'],
                'description' => $validated['org_description'] ?? null,
                'website' => $validated['org_website'] ?? null,
                'is_active' => true,
                // ✅ Link to agent
                'agent_id' => $agent->id,
                'registered_via_agent_at' => now(),
                'registration_source' => 'agent',
            ]);

            // 2. Create Primary User (Org Admin)
            $user = User::create([
                'name' => $validated['user_name'],
                'email' => $validated['user_email'],
                'password' => Hash::make($validated['user_password']),
                'organization_id' => $organization->id,
            ]);

            // 3. Assign org_admin role (assuming you have Spatie roles)
            $user->assignRole('org_admin');

            DB::commit();

            // 4. Send welcome email (optional)
            // Mail::to($user->email)->send(new OrganizationWelcomeEmail($organization, $user));

            // 5. Redirect to success page with login link
            return redirect()->route('agent.registration.success', [
                'org' => $organization->slug,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Registration failed. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show success page
     */
    public function success(Request $request)
    {
        $orgSlug = $request->get('org');
        $organization = Organization::where('slug', $orgSlug)->firstOrFail();

        return view('public.agent-registration-success', compact('organization'));
    }
}