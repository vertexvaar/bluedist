<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueFluid\Mvc\FluidAdapter;

abstract class AbstractController
{
    protected ServerRequestInterface $request;

    protected TemplateRendererInterface $templateRenderer;

    /**
     * @var bool Indicates if the template should be rendered after the action has been called
     */
    private bool $renderTemplate = true;

    final public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        if (class_exists(FluidAdapter::class)) {
            $this->templateRenderer = new FluidAdapter();
        } else {
            $this->templateRenderer = new TemplateRenderer();
        }
        if (is_callable([$this, 'initialize'])) {
            call_user_func([$this, 'initialize']);
        }
    }

    /**
     * @param array $configuration
     *
     * @return string
     *
     * @throws RedirectException
     */
    public function callActionMethod(array $configuration): string
    {
        $this->templateRenderer->setRouteConfiguration($configuration);
        $this->{$configuration['action']}();
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
