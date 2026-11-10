<?php

// Template mẫu. Placeholder: {name}, {code}, {position}, {email}, {invite_url}
return [
    'subject' => '[THK] Invitation to the Year End Party 2026',
    'body'    => <<<HTML
        <p>Dear <strong>{name}</strong> ({code}),</p>
        <p>
            We cordially invite you to attend the <strong>Year End Party 2026</strong>
            hosted by THK HOLDINGS VIETNAM.
        </p>
        <p>Your invitation and attendance details: <a href="{invite_url}">{invite_url}</a></p>
        <p>Further details will be shared at a later date.</p>
        <p>Best regards,<br>THK HOLDINGS VIETNAM</p>
        HTML,
];
