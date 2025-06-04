<?php

namespace App\Command;

use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'app:sitemap-generate',
	description: 'Генерирует карты сайта',
	aliases: ['s:g'],
)]
class SitemapGenerateCommand extends Command {

	/** @var PageRepository $pageRepo */
	private $pageRepo;

	public function __construct(
		private EntityManagerInterface $em,
	) {
		$this->pageRepo = $this->em->getRepository(Page::class);
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->addArgument('context', InputArgument::OPTIONAL, 'Критерий для изменения отдельных записей для конкретной карты сайта')
			->addArgument('update', InputArgument::OPTIONAL, 'Обновить карты сайта. Удаление несуществующих карт, если изменили название');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		/** @var Page[] $pages */
		$pages = $this->pageRepo->findAll();

		$this->generate($pages);

		return Command::SUCCESS;
	}

	private function generate(array $pages) {
		$sitemap = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');
		/** @var Page $page */
		foreach ($pages as $page) {
			$item = $sitemap->addChild("url");
			$item->addChild("loc", $this->getProtocol()
				. ($_SERVER['SERVER_NAME'] ?? $_ENV['SERVER_NAME'] ?? "localhost")
				. DIRECTORY_SEPARATOR
				. $page->getUri());
			$item->addChild('priority', '1.0');
			$item->addChild('lastmod', $page->getUpdatedAt()->format('Y-m-d'));
			$item->addChild('changefreq', 'monthly');

		}
		return file_put_contents(Page::getSitemapsDirectory() . '/sitemap.xml', $sitemap->asXML());
	}

	/**
	 * @return string
	 */
	private function getProtocol() {
		if (isset($_SERVER['HTTPS']) &&
			($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
			isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		return $protocol;
	}
}
