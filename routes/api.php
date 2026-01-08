<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerRegistrationController;
use App\Http\Controllers\PartnerVerificationController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\Api\TicketScanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\WhatsAppController;

Route::get('/partner/download', [PartnerVerificationController::class, 'downloadPartners']);
Route::get('/partner/verify/{id}', [PartnerVerificationController::class, 'verifyPartner']);
Route::get('/partner/sync-status', [PartnerVerificationController::class, 'syncStatus']);
Route::post('/checkin/bulk', [CheckInController::class, 'bulkCheckIn']);
Route::post('/checkin', [CheckInController::class, 'checkIn']);
Route::get('/checkins', [CheckInController::class, 'index']);
Route::get('/checkins/stats', [CheckInController::class, 'stats']);
Route::get('/checkins/status/{partnerId}', [CheckInController::class, 'checkStatus']);

// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Ticket Scanner Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    
    // Get events for the authenticated org
    Route::get('/scanner/events', [TicketScanController::class, 'getEvents']);
    
    // Download tickets for a specific event
    Route::get('/scanner/tickets/{eventId}', [TicketScanController::class, 'downloadTickets']);
    
    // Verify single ticket (optional - for testing)
    Route::get('/scanner/verify/{qrCode}', [TicketScanController::class, 'verifyTicket']);
    
    // Bulk check-in sync (upload scanned tickets)
    Route::post('/scanner/checkin/bulk', [TicketScanController::class, 'bulkCheckIn']);
    
    // Get check-in stats for an event
    Route::get('/scanner/stats/{eventId}', [TicketScanController::class, 'getStats']);
    
    // Sync status (check what needs syncing)
    Route::get('/scanner/sync-status', [TicketScanController::class, 'syncStatus']);
});

Route::post('/whatsapp/webhook', [WhatsAppController::class, 'webhook'])
    ->name('whatsapp.webhook');