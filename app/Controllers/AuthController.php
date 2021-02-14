<?php
/**
 * @author Antonio García García
 * 11/02/2021
 */

namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthController extends BaseController {
    public function getLogin() {
        return $this->renderHTML('login.twig');
    }

    public function postLogin($request) {
        $postData = $request->getParsedBody();
        $responseMessage = null;

        $user = User::where('email', $postData['email'])->first();
        if ($user) {
            if (password_verify($postData['password'], $user->password)) {
                $_SESSION['userId'] = $user->id;
                return new RedirectResponse('/ejercicios/bbdd/symblog/admin');
            } else {
                $responseMessage = "Bad Credentials";
            }
        } else {
            $responseMessage = "Bad Credentials";
        }
        return $this->renderHTML('login.twig', [
            'responseMessage' => $responseMessage
        ]);
    }

    public function getLogout() {
        unset($_SESSION['userId']);
        return new RedirectResponse('/ejercicios/bbdd/symblog/login');
    }
}
?>