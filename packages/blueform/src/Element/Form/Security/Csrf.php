<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueForm\Element\Form\Security;

use CoStack\Lib\Exceptions\ArrayKeyPathDoesNotExistException;
use CoStack\Lib\Exceptions\ArrayPathTerminatesEarlyException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use SensitiveParameter;
use VerteXVaaR\BlueForm\Element\Form\FormElement;
use VerteXVaaR\BlueForm\Exception\CsrfDefaultValueNotAllowedException;
use VerteXVaaR\BlueForm\Exception\CsrfInitialValueNotAllowedException;
use VerteXVaaR\BlueForm\Exception\EmptyCsrfFieldNameException;
use VerteXVaaR\BlueForm\Exception\EmptyCsrfSecretException;
use VerteXVaaR\BlueForm\Exception\EmptyCsrfTokenIdException;
use VerteXVaaR\BlueForm\Exception\InvalidCsrfTtlException;

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
            throw new EmptyCsrfTokenIdException();
        }

        if ($this->secret === '') {
            throw new EmptyCsrfSecretException();
        }

        if ($this->fieldName === '') {
            throw new EmptyCsrfFieldNameException();
        }

        if ($this->ttl < 1) {
            throw new InvalidCsrfTtlException();
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
        throw new CsrfDefaultValueNotAllowedException();
    }

    public function setInitialValue(mixed $initialValue): static
    {
        throw new CsrfInitialValueNotAllowedException();
    }
}
