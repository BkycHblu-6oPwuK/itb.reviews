<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    'js' => 'dist/script.js',
    'css' => 'dist/style.css',
    'rel' => [
        'itb.vue2_6',
        'itb.jq3_4',
        'itb.jq_fancybox5',
        'itb.notify',
    ],
    'skip_core' => true,
];
