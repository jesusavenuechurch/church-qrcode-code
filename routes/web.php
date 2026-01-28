<?php
use Illuminate\Support\Facades\Route;
use App\Models\Partner;
use App\Http\Controllers\PartnerRegistrationController;
use App\Http\Controllers\PartnerVerificationController;
use App\Http\Controllers\TicketDownloadController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\EventsBrowseController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\WhatsAppController;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\AgentRegistrationController;

Route::post('/contact', function (Request $request) {
    $data = $request->validate([
        'name'    => 'required|string|max:255',
        'phone'   => 'required|string|max:20', // Crucial for Lesotho/WhatsApp context
        'email'   => 'nullable|email',         // Now optional
        'subject' => 'required|string',
        'message' => 'required|string',
    ]);

    $emailContent = "New VENTIQ Intelligence Inquiry\n"
                  . "------------------------------\n"
                  . "Name: {$data['name']}\n"
                  . "Phone: {$data['phone']}\n"
                  . "Email: " . ($data['email'] ?? 'Not provided') . "\n"
                  . "Subject: {$data['subject']}\n\n"
                  . "Message:\n{$data['message']}";

    Mail::raw($emailContent, function ($message) use ($data) {
        $message->to('support@ventiq.co.ls')
                ->subject("Inquiry: {$data['subject']} - {$data['name']}");
    });

    return response()->json(['success' => true]);
});

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
        ->add(Url::create('/pricing')
            ->setLastModificationDate(now())
            ->setPriority(0.8)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
        ->add(Url::create('/events')
            ->setLastModificationDate(now())
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
    
    // Add all public events dynamically (without is_published check)
    try {
        $organizations = \App\Models\Organization::with('events')->get();
        
        foreach ($organizations as $org) {
            if ($org->events) {
                foreach ($org->events as $event) {
                    $sitemap->add(
                        Url::create("/org/{$org->slug}/event/{$event->slug}")
                            ->setLastModificationDate($event->updated_at ?? now())
                            ->setPriority(0.9)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    );
                }
            }
        }
    } catch (\Exception $e) {
        // Log error but still return sitemap with base URLs
        \Log::error('Sitemap generation error: ' . $e->getMessage());
    }
    
    return $sitemap->toResponse(request());
})->name('sitemap');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/events', [PublicEventController::class, 'browseAll'])
    ->name('events.browse');

// Partner Registration Routes
Route::get('/partner/register/{token}', [PartnerRegistrationController::class, 'show'])
    ->name('partner.register');
Route::post('/partner/register/{token}', [PartnerRegistrationController::class, 'store'])
    ->name('partner.store');
Route::get('partner/success', [PartnerRegistrationController::class, 'success'])
    ->name('partner.success');

// Partner QR Code Verification Route
Route::get('/partner/verify/{id}', [PartnerRegistrationController::class, 'verify'])
    ->name('partner.verify');

// Ticket Routes
Route::get('/ticket/{qr_code}', [TicketDownloadController::class, 'show'])->name('ticket.download');
Route::post('/ticket/{qr_code}/update-preference', [TicketDownloadController::class, 'updatePreference'])->name('ticket.update-preference');
Route::get('/ticket/{qr_code}/download', [TicketDownloadController::class, 'download'])->name('ticket.avatar.download');

// Organization & Event Routes
Route::prefix('org/{orgSlug}')->group(function () {
    // List all public events for an organization
    Route::get('/events', [PublicEventController::class, 'listEvents'])
        ->name('public.events');
    
    // View specific event
    Route::get('/event/{eventSlug}', [PublicEventController::class, 'show'])
        ->name('public.event');
});

// Optional: Short URL format
Route::get('/e/{orgSlug}/{eventSlug}', [PublicEventController::class, 'show'])
    ->name('event.short');

// Registration Routes
Route::prefix('register/{orgSlug}/{eventSlug}')->group(function () {
    // Show registration form
    Route::get('/', [RegistrationController::class, 'showForm'])
        ->name('registration.form');
    
    // Submit registration
    Route::post('/', [RegistrationController::class, 'register'])
        ->name('registration.submit');
    
    // Confirmation page
    Route::get('/confirmation/{ticketId}', [RegistrationController::class, 'confirmation'])
        ->name('registration.confirmation');
});

// Registration Error Page
Route::get('/{orgSlug}/{eventSlug}/register/error', function ($orgSlug, $eventSlug) {
    $organization = \App\Models\Organization::where('slug', $orgSlug)->firstOrFail();
    $event = \App\Models\Event::where('slug', $eventSlug)
        ->where('organization_id', $organization->id)
        ->firstOrFail();

    $error = session('error', 'Registration failed. Please try again.');
    $retryUrl = route('registration.form', ['orgSlug' => $orgSlug, 'eventSlug' => $eventSlug]); // ✅ Fixed

    return view('public.registration-error', compact('organization', 'event', 'error', 'retryUrl'));
})->name('registration.error');

// Installment payment routes
Route::prefix('installment')->name('installment.')->group(function () {
    Route::get('/search', [InstallmentController::class, 'search'])->name('search');
    Route::post('/find', [InstallmentController::class, 'find'])->name('find');
    Route::get('/{ticket}', [InstallmentController::class, 'show'])->name('show');
    Route::post('/{ticket}/pay', [InstallmentController::class, 'pay'])->name('pay');
});

Route::post('/whatsapp/webhook', [WhatsAppController::class, 'webhook'])
    ->name('whatsapp.webhook');

Route::get('/pricing', function () {
    return view('public.pricing');
})->name('pricing');


Route::get('/access', function () {
    return view('public.org-admin');
})->name('pricing');

Route::get('/org/register/{token}', [AgentRegistrationController::class, 'showForm'])
    ->name('agent.registration.form');

Route::post('/org/register/{token}', [AgentRegistrationController::class, 'submit'])
    ->name('agent.registration.submit');