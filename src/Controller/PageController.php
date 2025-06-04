<?php

namespace App\Controller;

use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController {

	private $managerRegistry;

	public function __construct(ManagerRegistry $managerRegistry) {
		$this->managerRegistry = $managerRegistry;
	}
	#[Route(path: '/page/load', name: 'page_load')]
	public function pageLoad(): Response {
		$fixture = new AppFixtures();
		$fixture->load($this->managerRegistry->getManager());

		return $this->redirect("/");
	}
}
