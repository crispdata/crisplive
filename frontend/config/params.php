<?php

return [
    'adminEmail' => 'admin@example.com',
    'BASE_URL' => 'https://crispdata.co.in/frontend/web/',
    'ROOT_URL' => 'https://crispdata.co.in/frontend/',
    'IMAGE_URL' => 'https://crispdata.co.in/frontend/web/',
    'AJAX_URL' => 'https://crispdata.co.in/index.php/',
    'googleAnalytics' => [
        'developerKey' => 'AIzaSyAZo9TShqXlMfP25AkFCIGlGE-RPjcYRig', // Public key
        'clientId' => '663066723913-90ai1qfjt029a8g3mu9d82v2tim9nk0s.apps.googleusercontent.com', // Client ID
        'analyticsId' => 'ga:189008508', //(It is the number at the end of the URL starting with p: https://www.google.com/analytics/web/#home/a33443w112345pXXXXXXXX/)
        'serviceAccountName' => 'crispdata@crispdata.iam.gserviceaccount.com', // Email address
        'privateKeyPath' => 'https://crispdata.co.in/frontend/web/assets/images/key.p12', //path to private key in p12 format
    ],
];
