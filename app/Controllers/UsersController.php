<?php
/**
 * @author Antonio García García
 * 09/02/2021
 */

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\Response\HtmlResponse as HtmlResponse;
use Respect\Validation\Validator as v;

class UsersController extends BaseController {
    public function getAddUserAction($request) {
        // Mensaje para indicar el resultado de la operación
        $responseMessage = null;
        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();
            // Añadimos validación utilizando la librería respect/validation
            $userValidator = v::key('email', v::stringType()->notEmpty())->key('password', v::stringType()->notEmpty());
            try {
                $userValidator->assert($postData);
                $user = new User();
                $user->email = $postData['email'];
                $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
                $user->save();
                $responseMessage = "Saved";
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }
        return $this->renderHTML('addUser.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}
?>