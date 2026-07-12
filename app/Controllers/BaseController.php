<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'ui'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** Render a page view wrapped in the main layout. */
    protected function render(string $view, array $data = []): string
    {
        $data['content_view'] = $view;
        return view('layout/main', $data);
    }

    /**
     * Validasi input — bila gagal, redirect back dengan error + input lama.
     * Mengembalikan true bila validasi lolos, atau ResponseInterface bila gagal.
     */
    protected function validateOrFail(array $rules, string $errorMsg = ''): true|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->validate($rules)) {
            return true;
        }
        $msg = $errorMsg ?: 'Mohon lengkapi seluruh data yang wajib diisi.';
        return redirect()->back()->withInput()->with('error', $msg);
    }

    /**
     * Filter POST data — hanya kembalikan field yang ada di $allowed.
     * Hindari mass assignment vulnerability.
     */
    protected function whitelist(array $allowed): array
    {
        $post = $this->request->getPost();
        return array_intersect_key($post, array_flip($allowed));
    }
}
