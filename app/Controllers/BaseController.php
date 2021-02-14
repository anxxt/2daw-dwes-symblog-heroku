<?php
/**
 * @author Antonio García García
 * 05/02/2021
 */

namespace App\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Laminas\Diactoros\Response\HtmlResponse;

class BaseController {
    protected $templateEngine;
    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader('../views');
        $this->templateEngine = new \Twig\Environment($loader, array(
            'debug' => true,
            'cache' => false
        ));
    }
    public function renderHTML($filename, $data=[]) {
        // Tags
        $blogs = Blog::all();
        $tags = [];
        foreach ($blogs as $blog) {
            $tags = array_merge($tags, explode(",", $blog->tags));
        }

        // Recent comments
        $allComments = Comment::all();
        // Convert object from Eloquent to php array and reverse it
        $allComments = array_reverse(json_decode(json_encode($allComments), true));
        $comments = [];
        $counter = 0;
        foreach ($allComments as $comment) {
            array_push($comments, $comment);
            $counter++;
            if ($counter >= 5) {
                // Limit to 5 comments
                break;
            }
        }

        // User loged
        $loged = null;
        if (isset($_SESSION['user'])) {
            $loged = $_SESSION['user'];
        }

        $data = array_merge($data, array("user" => $loged), array("tags" => $tags), array("latestComments" => $comments));
        return new HtmlResponse($this->templateEngine->render($filename, $data));
    }
}
?>