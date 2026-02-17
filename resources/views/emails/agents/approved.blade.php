<x-mail::message>
# Agent Partnership Approved

Hello {{ $user->name }},

We’re pleased to inform you that your application for the **VENTIQ Agent Partnership** has been approved.

You can now access your Agent Console and begin onboarding organizations.

To activate your account and set your credentials, click the button below:

<x-mail::button :url="$url" color="primary">
Activate Agent Account
</x-mail::button>

---

### Access Details
- **Username:** {{ $user->email }}
- **Role:** Authorized VENTIQ Agent

This activation link will expire in **24 hours**.

If you did not request this access, please ignore this email.

Welcome to the network.

**VENTIQ Operations**
</x-mail::message>