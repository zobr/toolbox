<?php

namespace Zobr\Toolbox\Slim;

use Slim\Http\Response;
use League\Plates\Engine as Plates;
use Zobr\Toolbox\MessageBag;
use Zobr\Toolbox\Session;

class Renderer {

    /**
     * Plates engine
     * @var \League\Plates\Engine
     */
    private $plates;

    /**
     * @param string $path Path to templates directory
     */
    public function __construct(string $path) {
        $this->plates = new Plates($path);
        if (is_dir($path . '/layouts')) {
            $this->plates->addFolder('layout', $path . '/layouts');
        }
        if (is_dir($path . '/components')) {
            $this->plates->addFolder('component', $path . '/components');
        }
    }

    /**
     * @return \League\Plates\Engine
     */
    public function unwrap() {
        return $this->plates;
    }

    /**
     * @param  \Zobr\Toolbox\Interfaces\MessageBag $errors
     * @return self
     */
    public function withErrors(MessageBag $errors) {
        $this->plates->addData([
            'errors' => $errors,
        ]);
        return $this;
    }

    /**
     * @param  \Zobr\Toolbox\Interfaces\Session $session
     * @return self
     */
    public function withSession(Session $session) {
        $this->plates->addData([
            'session' => $session,
        ]);
        return $this;
    }

    /**
     * Render a template
     * @param  \Slim\Http\Response $res
     * @param  string   $template
     * @param  array    $data     Data to inject into the template
     * @return self
     */
    public function render(Response $res, $template, array $data = []) {
        $output = $this->fetch($template, $data);
        $res->getBody()->write($output);
        return $this;
    }

    /**
     * Fetch a template
     * @param  string   $template
     * @param  array    $data     Data to inject into the template
     * @return string
     */
    public function fetch($template, array $data = []) {
        return $this->plates->render($template, $data);
    }

}
