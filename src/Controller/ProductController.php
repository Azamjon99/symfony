<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Form\Type\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
class ProductController extends AbstractController
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
        return $this->json($products);
    }

    public function createAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $form = $this->buildForm(ProductType::class);
                $entityManager = $doctrine->getManager();   
        // $form = $this->buildForm(ProductType::class);

        $product = new Product();
        $product->setName('Keyboard');
        $product->setColor('Keyboard');
        $product->setType('Keyboard');
        $entityManager->persist($product);       
         $entityManager->flush();
        return new Response('Saved new product with id '.$product->getId());
    }
}
