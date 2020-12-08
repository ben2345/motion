<?php
/**
 * Ajoute une authentification basique
 */

// Liste des adresses IP
$auth_remote_addr = [
    '192.168.1.0',
];

// Actif en preprod, mais pas pour l'API
$must_be_auth = isset($_SERVER['SERVER_NAME']);
$is_allowed   = false;
$client_ip    = $_SERVER['REMOTE_ADDR'] ?? null;

foreach ($auth_remote_addr as $addr) {
    // Vérifie les débuts d'adresse
    if (strpos($client_ip, $addr) === 0) {
        $is_allowed = true;
        break;
    }
}

if ($must_be_auth && !$is_allowed) {

    $realm = utf8_decode('Accès restreint');

    //user => password
    $users = ['ben' => 'ben'];

    if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid('', true) . '",opaque="' . md5($realm) . '"');

        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>401 Authorization Required</title>
</head><body>
<h1>Authorization Required</h1>
<p>This server could not verify that you
are authorized to access the document
requested.  Either you supplied the wrong
credentials (e.g., bad password), or your
browser doesn\'t understand how to supply
the credentials required.</p>
</body></html>';
        die();
    }

    // function to parse the http auth header
    function http_digest_parse($txt)
    {
        // protect against missing data
        $needed_parts = [
            'nonce'    => 1,
            'nc'       => 1,
            'cnonce'   => 1,
            'qop'      => 1,
            'username' => 1,
            'uri'      => 1,
            'response' => 1,
        ];
        $data         = [];
        $keys         = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

    // analyze the PHP_AUTH_DIGEST variable
    if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid('', true) . '",opaque="' . md5($realm) . '"');
        die();
    }

    // generate the valid response
    $A1             = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
    $A2             = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
    $valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

    if ($data['response'] != $valid_response) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid('', true) . '",opaque="' . md5($realm) . '"');
        die();
    }
}