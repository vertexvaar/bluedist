<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueFluid\Mvc\FluidAdapter;

use function call_user_func;
use function class_exists;
use function is_callable;

abstract class AbstractController implements Controller
{
    protected TemplateRendererInterface $templateRenderer;

    /**
     * @var bool Indicates if the template should be rendered after the action has been called
     */
    private bool $renderTemplate = true;

    /**
     * @param array $configuration
     *
     * @return string
     *
     * @throws RedirectException
     */
    public function callActionMethod(array $configuration, ServerRequestInterface $request): string
    {
        if (class_exists(FluidAdapter::class)) {
            $this->templateRenderer = new FluidAdapter();
        } else {
            $this->templateRenderer = new TemplateRenderer();
        }
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
