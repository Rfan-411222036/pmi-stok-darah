<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // When using PHP built-in server for local testing, skip auth filter
        if (php_sapi_name() === 'cli-server') {
            return;
        }
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            return redirect()->to('/login');
        }

        // Role-based access control
        if (!empty($arguments)) {
            $userRole = session()->get('role');
            
            // Jika route membutuhkan role tertentu
            if (!in_array($userRole, $arguments)) {
                if ($request->isAJAX()) {
                    return service('response')->setStatusCode(403)->setJSON([
                        'error' => 'Anda tidak memiliki akses ke halaman ini'
                    ]);
                }
                
                session()->setFlashdata('error', 'Anda tidak memiliki akses ke halaman ini!');
                return redirect()->to('/dashboard');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}