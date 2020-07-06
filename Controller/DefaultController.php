<?php
namespace Treetop1500\EasyadminDragndropSortBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use ReflectionClass;

/**
 * Class DefaultController
 *
 * @package Treetop1500\EasyadminDragndropSortBundle\Controller
 * @author http://github.com/treetop1500
 */
class DefaultController extends AbstractController
{

    /**
     * Resorts an item using it's doctrine sortable property
     *
     * @Route("/sort/{entityClass}/{id}/{position}", name="easyadmin_dragndrop_sort_sort")
     *
     * @param String $entityClass            
     * @param Integer $id            
     * @param Integer $position            
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     * @return Response
     *
     */
    public function sortAction($entityClass, $id, $position, Request $request)
    {
        $entityClassNameArray = explode("\\", $entityClass);
        $entityClassName = end($entityClassNameArray);
        try {
            $rc = new ReflectionClass($entityClass);
        } catch (\ReflectionException $error) {
            throw new \ReflectionException("The class name " . $entityClass . "  cannot be reflected.");
        }
        
        $em = $this->getDoctrine()->getManager();
        $e = $em->getRepository($rc->getName())
            ->find($id);
        if (is_null($e)) {
            throw new NotFoundHttpException("The entity was not found");
        }
        $e->setPosition($position);
        $em->persist($e);
        $em->flush();
        return $this->redirectToRoute("easyadmin", array(
            "action" => "list",
            "entity" => $entityClassName,
            "sortField" => "position",
            "sortDirection" => "ASC",
            "filters" => json_decode($request->query->get('filters'))
        ));
    }
}
