<?php
return [
    'unexpected_fields' => [
        'message' => 'Bad Request',
        'error' => 'Invalid parameters detected.',
    ],
    'filter' => [
        'string' => 'The filter must be a valid string.',
        'lowercase' => 'The filter must only contain lowercase letters.',
    ],
    'limit' => [
        'integer' => 'The limit value must be a number.',
        'min' => 'The limit value must be greater than 0.',
        'max' => 'The limit value must be less than 50.',
    ],
    'details'  => [
        'not_found' => [
            'message' => 'Cuisine not found.',
            'error' => 'The requested resource does not exist.',
        ]
    ]
];
