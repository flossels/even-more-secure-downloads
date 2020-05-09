<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extensionKey) {
        // Register default token
        \Leuchtfeuer\SecureDownloads\Registry\TokenRegistry::register(
            'tx_evenmoresecuredownloads_rsa',
            \Flossels\EvenMoreSecureDownloads\Domain\Transfer\Token\RsaToken::class,
            50,
            false
        );

        // Register default checks
        \Leuchtfeuer\SecureDownloads\Registry\CheckRegistry::register(
            'tx_evenmoresecuredownloads_once',
            \Flossels\EvenMoreSecureDownloads\Security\OneTimeCheck::class,
            50,
            true
        );
    }, 'even_more_secure_downloads'
);


