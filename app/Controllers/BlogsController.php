<?php

/**
 * @author Antonio García García
 * 05/02/2021
 */

namespace App\Controllers;

use App\Models\Blog;
use Laminas\Diactoros\Response\HtmlResponse as HtmlResponse;
use Respect\Validation\Validator as v;

class BlogsController extends BaseController {
    public function getAddBlogAction($request) {
        // Mensaje para indicar el resultado de la operación
        $responseMessage = null;
        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();
            // Añadimos validación utilizando la librería respect/validation
            $blogValidator = v::key('title', v::stringType()->notEmpty())->key('description', v::stringType()->notEmpty());
            try {
                $blogValidator->assert($postData);
                $blog = new Blog();
                $blog->title = $postData['title'];
                $blog->blog = $postData['description'];
                $blog->tags = $postData['tags'];
                $blog->author = $postData['author'];
                // Carga de archivos
                $files = $request->getUploadedFiles();
                $image = $files['image'];
                if ($image->getError() == UPLOAD_ERR_OK) {
                    $fileName = $image->getClientFilename();
                    $fileName = uniqid() . $fileName;
                    $image->moveTo("img/$fileName");
                    $blog->image = $fileName;
                }
                $blog->save();
                $responseMessage = "Saved";
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }
        return $this->renderHTML('addBlog.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}
