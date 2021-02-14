<?php
/**
 * @author Antonio García García
 * 11/02/2021
 */

namespace App\Controllers;

use App\Models\Blog;
use Laminas\Diactoros\Response\HtmlResponse as HtmlResponse;

class AdminController extends BaseController {
    public function getIndex() {
        return $this->renderHTML('admin.twig');
    }
}
?>