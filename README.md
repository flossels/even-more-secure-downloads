# Extending `EXT:secure_downloads`

These repository contains some code examples that show you how you can easily extend 
[secure_downloads](https://github.com/Leuchtfeuer/typo3-secure-downloads/) (version 5.0 or higher) with your own tokens and / or 
security checks.

## Create Your Own Token

In this example another JWT Library is used to generate tokens. The token is signed with an RSA key pair which makes the token 
even more secure.

### Register the Token

The token is registered in the 
[ext_localconf.php](https://github.com/flossels/even-more-secure-downloads/blob/master/ext_localconf.php#L7) file:

```
\Leuchtfeuer\SecureDownloads\Registry\TokenRegistry::register(
    'tx_evenmoresecuredownloads_rsa',
    \Flossels\EvenMoreSecureDownloads\Domain\Transfer\Token\RsaToken::class,
    50,
    false
);
```

## Add Your Own Security Check

This sample check prevents the repeated use of generated tokens. It checks whether the token has already been used previously. If 
this is the case, the access to the deposited file will be denied.

### Register the Check

The security check will also be registered in the 
[ext_localconf.php](https://github.com/flossels/even-more-secure-downloads/blob/master/ext_localconf.php#L15) file by calling the 
public method `registerCheck` of the `Leuchtfeuer\SecureDownloads\Registry\CheckRegistry` class:

```
\Leuchtfeuer\SecureDownloads\Registry\CheckRegistry::register(
    'tx_evenmoresecuredownloads_once',
    \Flossels\EvenMoreSecureDownloads\Security\OneTimeCheck::class,
    50,
    true
);
```

## Wanna Try the Code?

This repository is available via composer. You can execute following lines to add this repository to your TYPO3 instance:

```
composer config repositories.repo-name vcs https://github.com/flossels/even-more-secure-downloads.git
composer require flossels/even-more-secure-downloads:dev-master
```

Alternatively you can simply download this repository as a ZIP file, put it in the `typo3conf/ext` folder of your TYPO3 and 
unzip it. Please note that you may need to add autoload information.

## Disclaimer

Easy, isn't it?
Please note that this code is not intended for productive use (especially not the supplied RSA key pair). This repository is only 
meant to show you how you can extend secure_downloads with your own tokens and security checks.
