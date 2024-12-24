<?php

namespace App\Services;

use Soap\Psr18WsseMiddleware\WsseMiddleware;
use Soap\Psr18WsseMiddleware\WSSecurity\KeyStore\Certificate;
use Soap\Psr18WsseMiddleware\WSSecurity\KeyStore\Key;
use Soap\Psr18WsseMiddleware\WSSecurity\KeyIdentifier;
use Soap\Psr18WsseMiddleware\WSSecurity\Entry;

class Signature
{
    private string $privateKeyPath;
    private string $certificatePath;
    private mixed $passphrase;
    private array $signatureOptions;

    public function __construct($privateKeyPath, $certificatePath, $passphrase, $signatureOptions)
    {
        $this->privateKeyPath = $privateKeyPath;
        $this->certificatePath = $certificatePath;
        $this->passphrase = $passphrase;
        $this->signatureOptions = $signatureOptions;
    }

    public function getWsseMiddleware()
    {
        $privKey = Key::fromFile($this->privateKeyPath)->withPassphrase($this->passphrase);
        $pubKey = Certificate::fromFile($this->certificatePath);

        $timestampEntry = new Entry\Timestamp(120);
        $binarySecurityTokenEntry = new Entry\BinarySecurityToken($pubKey);
        $signatureEntry = (new Entry\Signature(
            $privKey,
            new KeyIdentifier\BinarySecurityTokenIdentifier()
        ))->withSignatureMethod($this->signatureOptions['signature_method'])
            ->withDigestMethod($this->signatureOptions['digest_method'])
            ->withSignAllHeaders($this->signatureOptions['sign_all_headers'])
            ->withSignBody($this->signatureOptions['sign_body']);

        return new WsseMiddleware([
            $timestampEntry,
            $binarySecurityTokenEntry,
            $signatureEntry,
        ]);
    }
}
