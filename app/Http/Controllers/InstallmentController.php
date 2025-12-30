<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\OrganizationPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    /**
     * Show the search form for finding a ticket
     */
    public function search()
    {
        return view('public.installment.search');
    }

    /**
     * Find a ticket by phone/name and ticket number
     * 
     */
    public function find(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
            'ticket_number_alt' => 'nullable|string',
            'phone' => 'nullable|string',
            'full_name' => 'nullable|string',
        ]);

        // Get the actual ticket number (could be from either field)
        $ticketNumber = $request->ticket_number ?: $request->ticket_number_alt;
        
        $ticket = null;

        // Search by phone if provided
        if ($request->phone) {
            // Clean phone number
            $phone = preg_replace('/\D/', '', $request->phone);
            if (!str_starts_with($phone, '266')) {
                $phone = '266' . $phone;
            }
            $phone = '+' . $phone;

            // Find ticket by phone
            $ticket = Ticket::where('ticket_number', $ticketNumber)
                ->whereHas('client', function ($query) use ($phone) {
                    $query->where('phone', $phone);
                })
                ->with(['event', 'tier', 'client', 'payments' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->first();
        }
        
        // Search by name if phone search failed or name provided
        elseif ($request->full_name) {
            // Find ticket by name (case-insensitive, partial match)
            $ticket = Ticket::where('ticket_number', $ticketNumber)
                ->whereHas('client', function ($query) use ($request) {
                    $query->where('full_name', 'LIKE', '%' . $request->full_name . '%');
                })
                ->with(['event', 'tier', 'client', 'payments' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->first();
        }

        if (!$ticket) {
            return back()->withErrors([
                'ticket_number' => 'Ticket not found. Please check your details and try again.',
            ])->withInput();
        }

        // Check if tier allows installments
        if (!$ticket->tier->allow_installments) {
            return back()->withErrors([
                'ticket_number' => 'This ticket does not support installment payments.',
            ])->withInput();
        }

        // Redirect to ticket installment page
        return redirect()->route('installment.show', $ticket->id);
    }

    /**
     * Show ticket details and payment form
     */
    public function show(Ticket $ticket)
    {
        // Load relationships
        $ticket->load(['event.organization', 'tier', 'client', 'payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Check if event allows installments
        if (!$ticket->event->allow_installments) {
            abort(403, 'This event does not support installment payments.');
        }

        // Get organization's payment methods
        $paymentMethods = OrganizationPaymentMethod::where('organization_id', $ticket->event->organization_id)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('public.installment.show', compact('ticket', 'paymentMethods'));
    }

    /**
     * Process installment payment
     */
    public function pay(Request $request, Ticket $ticket)
    {
        // Validate
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:organization_payment_methods,id',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        // Check if event allows installments
        if (!$ticket->event->allow_installments) {
            return back()->withErrors([
                'amount' => 'This event does not support installment payments.',
            ]);
        }

        // Check if amount exceeds remaining balance
        if ($request->amount > $ticket->remaining_amount) {
            return back()->withErrors([
                'amount' => 'Payment amount (' . config('constants.currency.symbol') . ' ' . number_format($request->amount, 2) . 
                           ') exceeds remaining balance (' . config('constants.currency.symbol') . ' ' . number_format($ticket->remaining_amount, 2) . ').',
            ])->withInput();
        }

        // Get payment method
        $paymentMethod = OrganizationPaymentMethod::findOrFail($request->payment_method_id);

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = TicketPayment::create([
                'ticket_id' => $ticket->id,
                'amount' => $request->amount,
                'payment_method' => $paymentMethod->payment_method,
                'payment_reference' => $request->payment_reference,
                'status' => 'pending',
                'payment_date' => now(),
                'payment_type' => 'installment',
            ]);

            DB::commit();

            return redirect()->route('installment.show', $ticket->id)
                ->with('success', 'Payment submitted successfully! Your payment of ' . 
                       config('constants.currency.symbol') . ' ' . number_format($request->amount, 2) . 
                       ' is pending approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Installment payment failed: ' . $e->getMessage());
            
            return back()->withErrors([
                'amount' => 'Payment submission failed. Please try again.',
            ])->withInput();
        }
    }
}