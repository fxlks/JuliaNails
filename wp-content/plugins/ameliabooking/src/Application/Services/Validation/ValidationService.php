<?php

namespace AmeliaBooking\Application\Services\Validation;

use AmeliaVendor\phpseclib3\Crypt\PublicKeyLoader;
use AmeliaVendor\phpseclib3\Crypt\RSA;

class ValidationService
{
    private const SITE_PUBLIC_KEYS = [
        'middleware' => <<<PEM
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuBviz1x8ocjJnoGmum6E
vBG1jz5Hnr/FNA9lYnoYVCP3P/gFm77AFP+vfl+jCLyWzB22uD8uoea/stUmP9U9
0bqVT5jMZorb4BoesPD8fG+iz8WzdfotRo/xatlhyZkE+9nE45BWHwZulf0ff1P8
IKi6FLtXukqngJ+m63fJ0GelAH8KuMj6gzM1O+SSojGO+HBCdwlCcrxBal5M2x3Z
dSKBh3U+qdCtnRSahkAO3DU7O2d9U+HZWBaKUJcb0LB1pM9mUlb3oFIaxQjLNdIk
krr9L9okRlHeB1MqxfraexHRMmcrq1jawAerFIwSQrcZHGiQalbi3PKYZFEJgxqX
WQIDAQAB
-----END PUBLIC KEY-----
PEM,
        'smsApi' => <<<PEM
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsQSBshtWYj1Rw/iGs0xp
29W/NKRhzLie3uwqYMptFtOJhqeSLjs3FY8d+1DPj/C54GO7oGLDwNCA20hM0GvY
MjedibdqTLZkBieOhfhXLMr4GvMN7vkquNw4AWuEZldWqwM8KWR0LTFkzrF4DfxE
80JvybkVnoai1M4ceD5nQ3IJg6C1JBMhG4OLo3rbO1tPOA/KtLbLOZrJwHXjcpNn
TLF+HlIjt8GAx3euu+mIcyPQy8wfDmLXLBcLU7F6lkQBNaIVznYbx106brgZj++b
K1QOKbCrIUcLeaproTvLkxiBMashor2MNwhWbCLtiQ2+Wbm1vruAXyJmmmXTw8B6
yQIDAQAB
-----END PUBLIC KEY-----
PEM
    ];

    /**
     * @param string $stringToVerify
     * @param string $site
     * @param string $signature
     *
     * @return bool
     */
    public static function verifySignature($stringToVerify, $site, $signature)
    {
        if (!$signature || !isset(self::SITE_PUBLIC_KEYS[$site]) || !$stringToVerify) {
            return false;
        }

        $signature = base64_decode($signature);

        /** @var RSA\PublicKey $publicKey */
        $publicKey = PublicKeyLoader::load(self::SITE_PUBLIC_KEYS[$site]);

        $publicKey = $publicKey
            ->withPadding(RSA::SIGNATURE_PKCS1)
            ->withHash('sha256');

        return $publicKey->verify($stringToVerify, $signature);
    }
}
