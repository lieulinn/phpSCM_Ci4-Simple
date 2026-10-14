<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url', 'number', 'text'];

    /**
     * Session service instance
     *
     * @var Session
     */
    protected $session;

    /**
     * Current authenticated user data
     */
    protected ?array $currentUser = null;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload session service
        $this->session = service('session');

        if ($this->session->get('isLoggedIn')) {
            $this->currentUser = [
                'id'        => $this->session->get('user_id'),
                'username'  => $this->session->get('username'),
                'full_name' => $this->session->get('full_name'),
                'email'     => $this->session->get('email'),
                'role_name' => $this->session->get('role_name'),
            ];
        }
    }

    /**
     * Helper to render views with global layout data
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts/main')
    {
        $data['currentUser'] = $this->currentUser;
        $data['content']     = view($view, $data);

        return view($layout, $data);
    }
}
