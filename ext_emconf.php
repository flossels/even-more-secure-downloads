<?php

$EM_CONF['even_more_secure_downloads'] = [
    'title' => 'Even More Secure Downloads',
    'description' => 'Examples for extending EXT:secure_downloads',
    'category' => 'fe',
    'version' => '1.0.0-dev',
    'state' => 'experimental',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author' => 'Florian Wessels',
    'author_email' => 'mail@flossels.de',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-7.4.99',
            'typo3' => '10.4.0-10.4.99',
            'secure_downloads' => '5.0.0-5.99.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
