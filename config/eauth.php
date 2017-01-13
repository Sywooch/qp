<?php
return [
    'class' => 'nodge\eauth\EAuth',
    'popup' => true, // Use the popup window instead of redirecting.
    'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache' on production environments.
    'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
    'httpClient' => [
        // uncomment this to use streams in safe_mode
        //'useStreamsFallback' => true,
    ],
    'services' => [ // You can change the providers and their classes.
        'google' => [
            // register your app here: https://code.google.com/apis/console/
            'class' => 'nodge\eauth\services\GoogleOAuth2Service',
            'clientId' => '35092284851-939vp4kdri7ao8tlnofru1fmfem74m9f.apps.googleusercontent.com',
            'clientSecret' => '8A6aOC30ZQBOtVJdjBDuI25o',
            'title' => 'Google',
        ],
//        'twitter' => [
//            // register your app here: https://dev.twitter.com/apps/new
//            'class' => 'nodge\eauth\services\TwitterOAuth1Service',
//            'key' => '...',
//            'secret' => '...',
//        ],
//        'yandex' => [
//            // register your app here: https://oauth.yandex.ru/client/my
//            'class' => 'nodge\eauth\services\YandexOAuth2Service',
//            'clientId' => '...',
//            'clientSecret' => '...',
//            'title' => 'Yandex',
//        ],
        'facebook' => [
            // register your app here: https://developers.facebook.com/apps/
            'class' => 'nodge\eauth\services\FacebookOAuth2Service',
            'clientId' => '157127531441787',
            'clientSecret' => '13d4aeac03613daa3012eac24c43732a',
        ],
//        'yahoo' => [
//            'class' => 'nodge\eauth\services\YahooOpenIDService',
//            //'realm' => '*.example.org', // your domain, can be with wildcard to authenticate on subdomains.
//        ],
//        'linkedin' => [
//            // register your app here: https://www.linkedin.com/secure/developer
//            'class' => 'nodge\eauth\services\LinkedinOAuth1Service',
//            'key' => '...',
//            'secret' => '...',
//            'title' => 'LinkedIn (OAuth1)',
//        ],
//        'linkedin_oauth2' => [
//            // register your app here: https://www.linkedin.com/secure/developer
//            'class' => 'nodge\eauth\services\LinkedinOAuth2Service',
//            'clientId' => '...',
//            'clientSecret' => '...',
//            'title' => 'LinkedIn (OAuth2)',
//        ],
//        'github' => [
//            // register your app here: https://github.com/settings/applications
//            'class' => 'nodge\eauth\services\GitHubOAuth2Service',
//            'clientId' => '...',
//            'clientSecret' => '...',
//        ],
//        'live' => [
//            // register your app here: https://account.live.com/developers/applications/index
//            'class' => 'nodge\eauth\services\LiveOAuth2Service',
//            'clientId' => '...',
//            'clientSecret' => '...',
//        ],
//        'steam' => [
//            'class' => 'nodge\eauth\services\SteamOpenIDService',
//            //'realm' => '*.example.org', // your domain, can be with wildcard to authenticate on subdomains.
//            'apiKey' => '...', // Optional. You can get it here: https://steamcommunity.com/dev/apikey
//        ],
//        'instagram' => [
//            // register your app here: https://instagram.com/developer/register/
//            'class' => 'nodge\eauth\services\InstagramOAuth2Service',
//            'clientId' => 'ded5211ee04b46a09c312e22f914dfd0',
//            'clientSecret' => '03cf3e7370304377bcd25cc38dde78ff ',
//        ],
        'vkontakte' => [
            // register your app here: https://vk.com/editapp?act=create&site=1
            'class' => 'nodge\eauth\services\VKontakteOAuth2Service',
            'clientId' => '5810800',
            'clientSecret' => 'Pxdoog8WZ6WGggdDyQtw',
        ],
        'mailru' => [
            // register your app here: http://api.mail.ru/sites/my/add
            'class' => 'nodge\eauth\services\MailruOAuth2Service',
            'clientId' => '...',
            'clientSecret' => '...',
        ],
//        'odnoklassniki' => [
//            // register your app here: http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=13992188
//            // ... or here: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
//            'class' => 'nodge\eauth\services\OdnoklassnikiOAuth2Service',
//            'clientId' => '...',
//            'clientSecret' => '...',
//            'clientPublic' => '...',
//            'title' => 'Odnoklas.',
//        ],
    ],
];
?>
