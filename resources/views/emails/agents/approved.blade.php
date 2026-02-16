<x-mail::message>
# Protocol Active

Hello {{ $user->name }},

Your application for the **VENTIQ Agent Partnership** has been **APPROVED**. 

To access your Agent Console and begin onboarding organizations, you must initialize your credentials.

<x-mail::button :url="$url" color="primary">
Initialize Account
</x-mail::button>

**System Access Details:**
* **Username:** {{ $user->email }}
* **Access Level:** Authorized Agent

*This link will expire in 24 hours.*

Best regards,<br>
**VENTIQ Operations**
</x-mail::message>