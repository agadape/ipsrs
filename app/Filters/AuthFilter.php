<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip auth for public asset routes (QR Scan & Ping)
        $segments = $request->getUri()->getSegments();
        if (isset($segments[0], $segments[1], $segments[2]) && $segments[0] === 'ipsrs' && $segments[1] === 'aset') {
            if (preg_match('/^[a-f0-9\-]{36}$/i', $segments[2])) {
                if (!isset($segments[3]) || in_array($segments[3], ['ping', 'qr'])) {
                    return; // Allow public access
                }
            }
        }

        if (!session('user_id')) {
            // Save the URL they were trying to access so we can redirect them back after login
            if ($request->getMethod() === 'get' && !str_contains(current_url(), '/login') && !str_contains(current_url(), '/logout')) {
                session()->set('redirect_url', current_url());
            }
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
