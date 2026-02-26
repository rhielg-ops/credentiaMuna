<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Re-check DB on every request — blocks URL bypass after deactivation
        $userId = session()->get('user_id');
        if ($userId) {
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            if (!$user || $user['status'] === 'inactive') {
                session()->destroy();
                return redirect()->to('/login')->with('deactivated', true);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
