<?php
use RobinDort\PslzmeLinks\Module\QueryDecryption;

// Init all css / js files
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/url-query-data-filter.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/cookie-extractor.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/api-request.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/redirect-cookie-acception.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/query-click-listener.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/pslzme-cookiebar-name-and-greeting-verifyer.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpslzmelinks/js/main.js|static";

$GLOBALS['FE_MOD']['pslzme']['query_decryption'] = \RobinDort\PslzmeLinks\Module\QueryDecryption::class;

?>