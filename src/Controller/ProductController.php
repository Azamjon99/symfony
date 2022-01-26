<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        // $form = $this->buildForm(ProductType::class);
        //         $entityManager = $doctrine->getManager();   
        // // $form = $this->buildForm(ProductType::class);

        // $product = new Product();
        // $product->setName('Keyboard');
        // $product->setColor('Keyboard');
        // $product->setType('Keyboard');
        // $entityManager->persist($product);       
        //  $entityManager->flush();
        // return new Response('Saved new product with id '.$product->getId());


        $form = $this->buildForm(ProductType::class);

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

    public function updateAction(ManagerRegistry $doctrine,Request $request,  $id)
    {
        $entityManager = $doctrine->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->respond(
                'No product found for id '.$id, Response::HTTP_NOT_FOUND
            );
        }
        $form = $this->buildForm(ProductType::class, $product, ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->persist($product);
        $entityManager->flush();
        return $this->respond($product);

        // dd($id);
    }

    public function showAction(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            throw new NotFoundHttpException('Product does not exist for this customer');
        }
        return $this->respond($product);

        // dd($id);
    }
}
