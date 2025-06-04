<?php

namespace App\Controller;

use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController {

	private $managerRegistry;

	public function __construct(ManagerRegistry $managerRegistry) {
		$this->managerRegistry = $managerRegistry;
	}
	#[Route(path: '/sitemap/generate', name: 'sitemap_generate')]
	public function sitemapGenerate(): Response {
		$fixture = new AppFixtures();
		$fixture->load($this->managerRegistry->getManager());

		return $this->redirect("/");
	}
}
