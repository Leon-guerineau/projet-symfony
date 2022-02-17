<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorController extends AbstractController
{
    public function show(FlattenException $exception)
    {
        $code = $exception->getStatusCode();
        $message = $exception->getStatusText();
        switch(substr($code,0,1)){
            case 2:
                $type = 'success';
                break;
            case 3:
                $type = 'warning';
                break;
            case 4:
            case 5:
                $type = 'danger';
                break;
        }
        $this->addFlash($type??'primary', $code.', '.$message);
        return $this->redirectToRoute('home');
    }
}