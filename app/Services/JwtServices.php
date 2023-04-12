<?php
namespace App\Services;

use DateTimeImmutable;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Storage;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtServices
{
    /**
     * Prepare Configuration Object for $user
     * @param User $user
     * @return Configuration
     */
    protected static function prepareConfiguration(User $user): Configuration
    {
        return Configuration::forAsymmetricSigner(
            new Signer\Rsa\Sha256(),
            InMemory::file(Storage::path('/keys/private-'.$user->id.'.pem')),
            InMemory::plainText(config('jwt.secret')),
        );
    }

    /**
     * Issue Token after successful authentication
     * @param User $user
     * @return string
     */
    public static function issue(User $user): string
    {
        $keys = openssl_pkey_new([
            'user_uuid' => $user->uuid,
            'issuer' => config('app.url'),
        ]);
        $publicKeyPem = openssl_pkey_get_details($keys)['key'];
        openssl_pkey_export($keys, $privateKeyPem);

        Storage::put('/keys/public-'.$user->id.'.pem', $publicKeyPem);
        Storage::put('/keys/private-'.$user->id.'.pem', $privateKeyPem);

        $configuration = self::prepareConfiguration($user);

        $algorithm    = new Sha256();
        $signingKey   = $configuration->signingKey();

        $now   = new DateTimeImmutable();

        $token = $configuration->builder(ChainedFormatter::default())
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy($user->uuid)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user_uuid', $user->uuid)
            ->withHeader('Authorization', 'Bearer')
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }

    /**
     * Validate Bearer Token from the request
     * @param string $auth
     * @return User|null
     */
    public static function validate(string $auth): ?User
    {
        try {
            $parser = new Parser(new JoseEncoder());
            $token = $parser->parse($auth);
    
            $now   = new DateTimeImmutable();
    
            if ($token->claims()->get('exp') < $now) {
                return null;
            }
    
            $uuid = $token->claims()->get('user_uuid');
    
            $user = User::whereUuid($uuid)->first();
    
            $configuration = self::prepareConfiguration($user);
            
            if ($user && self::validateToken($user, $token, $configuration)) {
                return $user;
            }
    
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Validate Token agains't permmitedFor, signedWith, and identifier
     * @param User $user
     * @param Token $token
     * @param Configuration $configuration
     * @return bool
     */
    private static function validateToken(User $user, Token $token, Configuration $configuration): bool
    {
        $signingKey = $configuration->signingKey();
        $validator = $configuration->validator();
        return $validator->validate($token, new IdentifiedBy($user->uuid))
            && new PermittedFor(config('app.url'))
            && new SignedWith(new Signer\Rsa\Sha256(), $signingKey);
    }
}