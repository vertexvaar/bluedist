<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use Psr\Http\Message\ServerRequestInterface;

use function call_user_func;
use function is_callable;

abstract class AbstractController implements Controller
{

    /**
     * @var bool Indicates if the template should be rendered after the action has been called
     */
    private bool $renderTemplate = true;

    public function __construct(
        protected readonly Repository $repository,
        protected readonly TemplateRenderer $templateRenderer
    ) {
    }

    /**
     * @throws RedirectException
     */
    public function callActionMethod(array $configuration, ServerRequestInterface $request): string
    {
        if (is_callable([$this, 'initialize'])) {
            call_user_func([$this, 'initialize']);
        }

        $this->templateRenderer->setRouteConfiguration($configuration);
        $this->{$configuration['action']}($request);
        if (true === $this->renderTemplate) {
            return $this->templateRenderer->render();
        }
        return '';
    }

    protected function redirect($url, $code = RedirectException::SEE_OTHER): void
    {
        throw RedirectException::forUrl($url, $code);
    }
}
