<?php

return [
    'recipients' => array_values(array_filter(array_map(
        static fn (string $email): string => trim($email),
        explode(',', env('SUPPORT_RECIPIENTS', 'info@ghostfrog.co.uk,garyconstable80@gmail.com'))
    ))),
];
