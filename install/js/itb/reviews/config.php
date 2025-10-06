<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    'js' => 'dist/script.js',
    'css' => 'dist/style.css',
    'rel' => [
        'beeralex.vue2_6',
        'beeralex.jq3_4',
        'beeralex.jq_fancybox5',
        'beeralex.notify',
    ],
    'skip_core' => true,
];
