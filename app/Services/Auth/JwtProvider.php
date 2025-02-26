<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Entities\User;
use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Config\Repository;
use InvalidArgumentException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Validator;
use RuntimeException;

class JwtProvider
{
    /**
     * @var non-empty-string
     */
    private string $jwtSecret;

    private int $jwtTtl;

    public function __construct(Repository $config)
    {
        $this->jwtSecret = $config->get('jwt.secret', '');
        $this->jwtTtl = (int) $config->get('jwt.ttl', 900);
        if (empty($this->jwtSecret)) {
            throw new InvalidArgumentException('The JWT secret is not set');
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public function createAccessToken(User $user): string
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::base64Encoded($this->jwtSecret);

        $now = new DateTimeImmutable();
        $expiration = $now->modify("+$this->jwtTtl second");

        if ($expiration === false) {
            throw new RuntimeException('Cannot compute token expiry time.');
        }

        $token = $tokenBuilder
            ->relatedTo((string) $user->getAuthIdentifier())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiration)
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }

    public function parseJwtKey(string $jwtKey): array
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwtKey);

        if (!$token instanceof UnencryptedToken) {
            throw new InvalidArgumentException('The provided token is not valid');
        }

        $signingKey = InMemory::base64Encoded($this->jwtSecret);

        $validator = new Validator();
        if (!$validator->validate($token, new StrictValidAt(SystemClock::fromUtc()))) {
            throw new InvalidArgumentException('Claim checks failed');
        }

        if (!$validator->validate($token, new SignedWith(new Sha256(), $signingKey))) {
            throw new InvalidArgumentException('Signature checks failed');
        }

        return $token->claims()->all();
    }
}
