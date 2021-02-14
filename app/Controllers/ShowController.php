<?php
/**
 * @author Antonio García García
 * 08/02/2021
 */

namespace App\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Laminas\Diactoros\Response\HtmlResponse as HtmlResponse;
use Respect\Validation\Validator as v;

class ShowController extends BaseController {
    
    private function getBlog() {
        foreach (Blog::all() as $blog) {
            if ($blog->id == $_GET['id']) {
                $blogFinal = $blog;
            }
        }
        return $blogFinal;
    }
    public function showAction() {
        $blog = $this->getBlog();
        $comments = $blog->comments()->get();
        return $this->renderHTML('show.twig', array('blog' => $blog, 'comments' => $comments));
    }

    public function postComment($request) {
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
            $responseMessage = null;
            $error = false;
            $validator = v::key('autor', v::stringType()->notEmpty())->key('comentario', v::stringType()->notEmpty());
            
            try {
                $blog = $this->getBlog();
                // Validate
                $validator->assert($post);
                $comment = new Comment();
                $comment->blog_id = $blog->id;
                $comment->user = $post['autor'];
                $comment->comment = $post['comentario'];
                $comment->approved = 1;
                $comment->save();
                $responseMessage = "Comentario guardado";
            } catch (\Exception $e) {
                $responseMessage = $e;
                $error = true;
            }
        }
        return $this->renderHTML('show.twig', [
            'blog' => $this->getBlog(),
            'comments' => $blog->comments()->get(),
            'responseMessage' => $responseMessage
        ]);
    }
}
?>