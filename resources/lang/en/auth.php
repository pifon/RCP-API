<?php

return [
    'unexpected_fields' => [
        'message' => 'Bad Request',
        'error' => 'Invalid parameters detected.',
    ],
    'username' => [
        'string' => 'The username field must be a string.',
        'required' => 'The username field is required.',
        'max' => 'The username field is too long.',
    ],
    'password' => [
        'string' => 'The password field must be a string.',
        'required' => 'The password field is required.',
        'max' => 'The password field is too long.',
    ],
];
