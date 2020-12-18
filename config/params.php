<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'amocrm' => json_decode(file_get_contents('tmp/token.json'), true),
];
