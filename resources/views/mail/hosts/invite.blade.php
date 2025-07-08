<x-mail::message>
# You're invited to host a game!

Hey there! You've been invited to co-host an exciting game.

**Game Details:**
- **Show:** {{ $show->name }}
- **Host:** {{ $inviter->name }}

Join as a co-host and help manage questions, control voting, and reveal results!

<x-mail::button :url="route('show-host-invite', $invitation->token)">
Join as Host
</x-mail::button>

If you have any questions, feel free to reach out to {{ $inviter->name }}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
