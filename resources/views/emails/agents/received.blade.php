<x-mail::message>
# Application Received

Hello {{ $agent->name }},

Thank you for applying to become a **VENTIQ Agent Partner**.

This email confirms that we’ve successfully received your application.  
Your profile is currently under review.

**Current status:** `UNDER REVIEW`

Our team is assessing your application for the **{{ $agent->city_district }}** area.  
If approved, you’ll receive a secure email link to activate your **Agent Console** and begin onboarding organizations.

### What happens next
- 🕒 **Review period:** 24–48 hours  
- 📩 **Next step:** Email with your Agent access link and setup instructions  

<x-mail::button :url="config('app.url')" color="primary">
Visit Ventiq
</x-mail::button>

If you have any questions during this period, feel free to reply to this email.

Regards,  
**VENTIQ Team**
</x-mail::message>