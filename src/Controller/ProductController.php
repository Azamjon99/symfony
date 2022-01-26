<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
class ProductController extends AbstractApiController
{
    private $em;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->em = $registry;
    }
    public function indexAction(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        // $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        // dd($products);
        return $this->respond($products);
    }

    public function createAction(ManagerRegistry $doctrine, Request $request): Response
    {

        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();
        // dd($product);
        $doctrine->getManager()->persist($product);
        $doctrine->getManager()->flush();
        return $this->respond($product);

        // return $this->respond($product);
    }

    public function updateAction(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            return $this->respond("Product does not exist for this customer", Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm( new ProductType(), $product);
        $form->submit($data);
        
    //     $form = $this->buildForm(ProductType::class, $product, 
    //     [
    //         'action' => $this->generateUrl('products_update', ['id' => $id]),
    //         'method'=>'PATCH']
    // );
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();
        // dd($product);
        $doctrine->getManager()->persist($product);
        $doctrine->getManager()->flush();
        return $this->respond($product);
    }

    public function showAction(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            return $this->respond("Product does not exist ", Response::HTTP_NOT_FOUND);

        }
        return $this->respond($product);

        // dd($id);
    }

    public function deleteAction(ManagerRegistry $doctrine, int $id)
    {
        ;

        $cart = $this->em->getRepository(Product::class)->findOneBy([
            'id' => $id,
        ]);

        if (!$cart) {
            return $this->respond("Product does not exist ", Response::HTTP_NOT_FOUND);
        }

        $doctrine->getManager()->remove($cart);
        $doctrine->getManager()->flush();

        return $this->respond(null);
    }
}
