<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Form\Security;

use CoStack\Lib\Exceptions\ArrayKeyPathDoesNotExistException;
use CoStack\Lib\Exceptions\ArrayPathTerminatesEarlyException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use SensitiveParameter;
use VerteXVaaR\BlueForm\Element\Form\FormElement;

use function base64_encode;
use function CoStack\Lib\array_value;
use function random_bytes;
use function time;

class Csrf extends FormElement
{
    public function __construct(
        private readonly string $tokenId,
        #[SensitiveParameter] private readonly string $secret,
        private readonly string $fieldName = '_csrf_token',
        private readonly int $ttl = 1800,
        private ?int $now = null,
    ) {
        parent::__construct('csrf');

        if ($this->tokenId === '') {
            throw new InvalidArgumentException('Token ID must not be empty.');
        }

        if ($this->secret === '') {
            throw new InvalidArgumentException('Secret must not be empty.');
        }

        if ($this->fieldName === '') {
            throw new InvalidArgumentException('Field name must not be empty.');
        }

        if ($this->ttl < 1) {
            throw new InvalidArgumentException('TTL must be greater than 0.');
        }

        $this->condition = function (): bool {
            return $this->context === null || !$this->context->isShow();
        };
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        $parsedBody = $request->getParsedBody();
        try {
            $pathString = $this->getPathString();
            $value = array_value($parsedBody, $pathString);
        } catch (ArrayKeyPathDoesNotExistException|ArrayPathTerminatesEarlyException) {
            $value = null;
        }
        $jwt = JWT::decode($value, new Key($this->secret, 'HS256'));
        $this->setSubmittedValue($jwt);
    }

    public function getValue(): mixed
    {
        $issuedAt = $this->now ??= time();

        $payload = [
            'tid' => $this->tokenId,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->ttl,
            'rnd' => base64_encode(random_bytes(32)),
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function setDefaultValue(mixed $defaultValue): static
    {
        throw new LogicException('Calling setDefaultValue on CSRF is not allowed');
    }

    public function setInitialValue(mixed $initialValue): static
    {
        throw new LogicException('Calling setInitialValue on CSRF is not allowed');
    }
}
