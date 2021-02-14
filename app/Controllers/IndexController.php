<?php
/**
 * @author Antonio García García
 * 04/02/2021
 */

namespace App\Controllers;

use App\Models\Blog;
use Laminas\Diactoros\Response\HtmlResponse as HtmlResponse;

class IndexController extends BaseController {
    public function indexAction() {
        return $this->renderHTML('index.twig', array('blogs' => Blog::all()));
    }
}
?>