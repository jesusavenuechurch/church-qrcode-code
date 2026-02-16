@component('mail::message')
# Payout Confirmed 💸

Hi {{ $agent->name }},

We've just processed a payout for your account. The funds are on their way to you.

**Payout Details:**
@component('mail::table')
| Description | Amount |
| :--- | :--- |
| **Total Paid** | **M {{ number_format($totalAmount, 2) }}** |
| Method | {{ strtoupper(str_replace('_', ' ', $paymentMethod)) }} |
| Reference | `{{ $paymentReference }}` |
@endcomponent

**Summary of Commissions included in this payout:**
@foreach($earnings as $earning)
* {{ $earning->notes }} (M {{ number_format($earning->amount, 2) }})
@endforeach

Your dashboard has been updated. Keep up the great work onboarding organizations!

@component('mail::button', ['url' => config('app.url') . '/admin'])
View Dashboard
@endcomponent

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent