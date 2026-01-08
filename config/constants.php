<?php

// config/constants.php

return [
    'currency' => [
        'code' => 'LSL',
        'symbol' => 'L',
        'name' => 'Lesotho Loti',
        'decimals' => 2,
    ],

    'payment_methods' => [
        'cash' => [
            'label' => 'Cash Payment',
            'icon' => 'fa-money-bill-wave',
            'color' => 'text-green-600',
            'account_label' => null, // Cash doesn't need account
            'requires_account' => false,
        ],
        'ecocash' => [
            'label' => 'EcoCash',
            'icon' => 'fa-mobile-alt',
            'color' => 'text-blue-600',
            'account_label' => 'EcoCash Number',
            'requires_account' => true,
        ],
        'mpesa' => [
            'label' => 'M-Pesa',
            'icon' => 'fa-mobile-alt',
            'color' => 'text-red-600',
            'account_label' => 'M-Pesa Number',
            'requires_account' => true,
        ],
        'bank_transfer' => [
            'label' => 'Bank Transfer',
            'icon' => 'fa-university',
            'color' => 'text-purple-600',
            'account_label' => 'Bank Account Number',
            'requires_account' => true,
        ],
        'card' => [
            'label' => 'Card Payment',
            'icon' => 'fa-credit-card',
            'color' => 'text-orange-600',
            'account_label' => 'Merchant Code',
            'requires_account' => true,
        ],
        'online' => [
            'label' => 'Online Payment',
            'icon' => 'fa-globe',
            'color' => 'text-blue-600',
            'account_label' => 'Payment Gateway',
            'requires_account' => true,
        ],
        'free' => [
            'label' => 'Free',
            'icon' => 'fa-gift',
            'color' => 'text-gray-600',
            'account_label' => null,
            'requires_account' => false,
        ],
    ],

    'payment_statuses' => [
        'pending' => 'Pending',
        'partial' => 'Partial Payment',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ],

    'ticket_statuses' => [
        'pending' => 'Pending',
        'active' => 'Active',
        'checked_in' => 'Checked In',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    'delivery_methods' => [
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        'sms' => 'SMS',
        'in_person' => 'In Person',
    ],

    'event_statuses' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

     'delivery_methods' => [
        'whatsapp' => [
            'label' => 'WhatsApp',
            'icon' => 'fa-brands fa-whatsapp',
            'color' => 'text-green-600',
        ],
        'email' => [
            'label' => 'Email',
            'icon' => 'fa-envelope',
            'color' => 'text-blue-600',
        ],
        'both' => [
            'label' => 'WhatsApp & Email',
            'icon' => 'fa-paper-plane',
            'color' => 'text-purple-600',
        ],
    ],

    'delivery_statuses' => [
        'pending' => [
            'label' => 'Pending Delivery',
            'color' => 'warning',
            'icon' => 'fa-clock',
        ],
        'sent' => [
            'label' => 'Sent',
            'color' => 'info',
            'icon' => 'fa-paper-plane',
        ],
        'delivered' => [
            'label' => 'Delivered',
            'color' => 'success',
            'icon' => 'fa-check-circle',
        ],
        'failed' => [
            'label' => 'Failed',
            'color' => 'danger',
            'icon' => 'fa-exclamation-circle',
        ],
    ],
];
