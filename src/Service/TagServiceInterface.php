<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikolayyotsov
 * Date: 2/12/17
 * Time: 1:18 AM
 */

namespace Tag\Service;


interface TagServiceInterface
{
    public function search(string $tag, string $context);
}
