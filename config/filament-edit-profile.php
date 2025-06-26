<?php

return [
    'avatar_column' => 'avatar_url',
    'disk' => env('FILESYSTEM_DISK', 'public'),
    'visibility' => 'public', // or replace by filesystem disk visibility with fallback value
    'custom_fields' => [
    [
        'name' => 'phone_number',
        'label' => 'Phone Number',
        'component' => \Filament\Forms\Components\TextInput::class,
        'rules' => ['nullable', 'string', 'max:20'],
    ],
    [
        'name' => 'address',
        'label' => 'Address',
        'component' => \Filament\Forms\Components\Textarea::class,
        'rules' => ['nullable', 'string'],
    ],
],
];
