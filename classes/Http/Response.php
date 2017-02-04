<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

/**
 * Class Response
 */
class Response
{
    /**
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'text/html; charset=utf-8',
    ];

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function appendContent($content = '')
    {
        $this->content .= $content;
        return $this;
    }

    /**
     * @return void
     */
    public function respond()
    {
        foreach ($this->headers as $key => $value) {
            header($key . ':' . $value);
        }
        echo $this->content;
    }
}
