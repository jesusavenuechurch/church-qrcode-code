<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\OrganizationPaymentMethod;
use App\Models\Event;
use App\Models\EventTier;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketPayment;
use Illuminate\Support\Str;

class InstallmentTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create/Get Organization
        $org = Organization::firstOrCreate(
            ['slug' => 'expressions-ls'],
            [
                'name' => 'Expressions Conference',
                'email' => 'info@expressions.ls',
                'phone' => '+266 5949 4756',
                'description' => 'Empowering women through conferences and events',
                'is_active' => true,
                'tagline' => 'Bloom in Power',
                'contact_email' => 'contact@expressions.ls',
            ]
        );

        // 2. Create Payment Methods
        $paymentMethods = [
            [
                'payment_method' => 'mpesa',
                'account_name' => 'Expressions Conference',
                'account_number' => '+266 5949 4756',
                'instructions' => 'Send payment with reference: EVENT-[Your Name]',
                'display_order' => 1,
            ],
            [
                'payment_method' => 'ecocash',
                'account_name' => 'Expressions Conference',
                'account_number' => '+266 6307 5574',
                'instructions' => 'Send payment and screenshot confirmation',
                'display_order' => 2,
            ],
            [
                'payment_method' => 'cash',
                'account_name' => null,
                'account_number' => null,
                'instructions' => 'Pay at venue reception during business hours',
                'display_order' => 3,
            ],
        ];

        foreach ($paymentMethods as $method) {
            OrganizationPaymentMethod::firstOrCreate(
                [
                    'organization_id' => $org->id,
                    'payment_method' => $method['payment_method'],
                ],
                array_merge($method, ['is_active' => true])
            );
        }

        // 3. Create Event with Installments ENABLED
        $event = Event::firstOrCreate(
            ['slug' => 'bloom-in-power-2026'],
            [
                'organization_id' => $org->id,
                'name' => 'Bloom in Power 2026',
                'tagline' => 'A celebration of women empowerment',
                'description' => 'Join us for an inspiring day of networking, workshops, and empowerment.',
                'event_date' => '2026-03-28 11:00:00',
                'registration_deadline' => '2026-03-14 23:59:00',
                'venue' => 'Mpilo Boutique Hotel',
                'location' => 'Maseru, Lesotho',
                'capacity' => 200,
                'status' => 'published',
                'is_public' => true,
                'allow_installments' => true, // ✅ ENABLED
                'minimum_deposit_percentage' => 31.58, // M300/M950 = 31.58%
                'installment_instructions' => 'Pay minimum 32% deposit (M300) to secure your spot. Complete full payment before March 14th, 2026.',
            ]
        );

        // 4. Create Event Tiers
        $tier = EventTier::firstOrCreate(
            [
                'event_id' => $event->id,
                'tier_name' => 'Standard Ticket',
            ],
            [
                'price' => 950.00,
                'description' => 'Includes: Buffet Lunch & Refreshments, Access to all sessions & panels, Networking opportunities, Live music performance, Gift bag',
                'quantity_available' => 200,
                'quantity_sold' => 0,
                'is_active' => true,
                'color' => '#EC4899',
            ]
        );

        // 5. Create Test Clients
        $client1 = Client::firstOrCreate(
            ['phone' => '+26659494756', 'organization_id' => $org->id],
            [
                'full_name' => 'Neo Mohlomi',
                'email' => 'neo@example.com',
                'status' => 'active',
                'notes' => 'Test client for installments',
            ]
        );

        $client2 = Client::firstOrCreate(
            ['phone' => '+26663075574', 'organization_id' => $org->id],
            [
                'full_name' => 'Lineo Maseko',
                'email' => 'lineo@example.com',
                'status' => 'active',
                'notes' => 'Test client for installments',
            ]
        );

        // 6. Create Test Tickets with Different Payment Scenarios

        // Scenario 1: Deposit paid, pending approval
        $ticket1 = Ticket::firstOrCreate(
            ['ticket_number' => 'TKT-' . $event->id . '-00001'],
            [
                'event_id' => $event->id,
                'client_id' => $client1->id,
                'event_tier_id' => $tier->id,
                'qr_code' => 'QR-' . Str::uuid(),
                'status' => 'pending',
                'payment_method' => 'mpesa',
                'amount' => 950.00,
                'amount_paid' => 0,
                'payment_status' => 'pending',
                'payment_reference' => 'MPESA-TEST001',
            ]
        );

        TicketPayment::firstOrCreate(
            [
                'ticket_id' => $ticket1->id,
                'payment_reference' => 'MPESA-TEST001',
            ],
            [
                'amount' => 300.00,
                'payment_method' => 'mpesa',
                'status' => 'pending',
                'payment_date' => now(),
                'payment_type' => 'deposit',
            ]
        );

        // Scenario 2: Deposit approved, one installment pending
        $ticket2 = Ticket::firstOrCreate(
            ['ticket_number' => 'TKT-' . $event->id . '-00002'],
            [
                'event_id' => $event->id,
                'client_id' => $client2->id,
                'event_tier_id' => $tier->id,
                'qr_code' => 'QR-' . Str::uuid(),
                'status' => 'active',
                'payment_method' => 'ecocash',
                'amount' => 950.00,
                'amount_paid' => 300.00,
                'payment_status' => 'partial',
            ]
        );

        // Deposit - approved
        TicketPayment::firstOrCreate(
            [
                'ticket_id' => $ticket2->id,
                'payment_reference' => 'ECO-DEPOSIT-001',
            ],
            [
                'amount' => 300.00,
                'payment_method' => 'ecocash',
                'status' => 'approved',
                'payment_date' => now()->subDays(10),
                'approved_at' => now()->subDays(10),
                'payment_type' => 'deposit',
            ]
        );

        // Installment 1 - pending
        TicketPayment::firstOrCreate(
            [
                'ticket_id' => $ticket2->id,
                'payment_reference' => 'ECO-INST-001',
            ],
            [
                'amount' => 350.00,
                'payment_method' => 'ecocash',
                'status' => 'pending',
                'payment_date' => now(),
                'payment_type' => 'installment',
            ]
        );

        $this->command->info('✅ Installment test data created successfully!');
        $this->command->info('');
        $this->command->info('📋 Test Data Summary:');
        $this->command->info("Organization: {$org->name}");
        $this->command->info("Event: {$event->name} (Installments: ENABLED)");
        $this->command->info("Payment Methods: M-Pesa, EcoCash, Cash");
        $this->command->info('');
        $this->command->info('🎫 Test Tickets:');
        $this->command->info("1. {$ticket1->ticket_number} - Neo (+266 5949 4756) - Deposit pending approval");
        $this->command->info("2. {$ticket2->ticket_number} - Lineo (+266 6307 5574) - Deposit approved, installment pending");
        $this->command->info('');
        $this->command->info('🧪 Test Scenarios:');
        $this->command->info('• Admin: Approve pending payments in Tickets resource');
        $this->command->info('• Public: Visit /installment/search and search with phone & ticket number');
        $this->command->info("  Example: +266 6307 5574, {$ticket2->ticket_number}");
    }
}