<?php

namespace App\Controller;

use App\Entity\Page;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController {

	private $managerRegistry;

	public function __construct(ManagerRegistry $managerRegistry) {
		$this->managerRegistry = $managerRegistry;
	}

	#[Route('/')]
	public function index(): Response {
		return $this->render('index/index.html.twig', [
			'items' => $this->managerRegistry->getRepository(Page::class)->findAll()
		]);
	}
}
