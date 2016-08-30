<?php

namespace Zobr\Toolbox\Slim;

use Slim\Http\Response;

abstract class Controller {

    /**
     * @var Renderer
     */
    protected $renderer;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * Respond with 404
     * @param  Response $res
     * @return Response
     */
    public function notFound(Response $res) {
        $res = $res->withStatus(404);
        $this->renderer->render($res, '404');
        return $res;
    }

    /**
     * Redirect to URL
     * @param  Response $res
     * @param  string   $url
     * @return Response
     */
    public function redirect(Response $res, $url) {
        return $res->withStatus(301)->withHeader('Location', $url);
    }

}
