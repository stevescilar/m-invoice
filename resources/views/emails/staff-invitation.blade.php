@component('mail::message')

# You're invited! 🎉

Hi **{{ $invitation->name }}**,

**{{ $invitation->invitedBy->name }}** has invited you to join **{{ $invitation->company->name }}** on M-Invoice as a staff member.

Click the button below to accept the invitation and set up your account. This link expires in **48 hours**.

@component('mail::button', ['url' => route('staff.accept', $invitation->token), 'color' => 'green'])
Accept Invitation
@endcomponent

If you did not expect this invitation, you can safely ignore this email.

Regards,
**M-Invoice**

@endcomponent