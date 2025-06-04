<?php

namespace App\Command;

use App\Entity\Page;
use App\Entity\Template;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

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
		private KernelInterface $appKernel
	) {
		$this->pageRepo = $this->em->getRepository(Page::class);
		$this->sitemapNames = explode(',',$_ENV['SITEMAP_NAMES']);
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->addArgument('context', InputArgument::OPTIONAL, 'Критерий для изменения отдельных записей для конкретной карты сайта')
			->addArgument('update', InputArgument::OPTIONAL, 'Обновить карты сайта. Удаление несуществующих карт, если изменили название');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {

		$this->index($this->sitemapNames);

		$context = $input->getArgument('context');
		if (is_string($context) && in_array($context, $this->sitemapNames)) {
			$this->sitemapNames = [$context];
		}

		$update = $input->getArgument('update');

		if (is_string($update) && $update === 'update') {
			$this->removeAbsent($output);
		}

		/** @var Page[] $pages */
		foreach ($this->sitemapNames as $sitemapName) {
			$pages = $this->pageRepo->findByName(['context' => $sitemapName]);
			$this->generateByName($sitemapName, $pages);
		}

		return Command::SUCCESS;
	}

	private function index(array $context) {
		$sitemap = new SimpleXMLElement('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></sitemapindex>');
		foreach ($context as $contextItem) {
			$item = $sitemap->addChild("sitemap");
			$item->addChild("loc", $this->getProtocol()
				. ($_SERVER['SERVER_NAME'] ?? $_ENV['SERVER_NAME'] ?? "localhost") . DIRECTORY_SEPARATOR
				. 'sitemaps'. DIRECTORY_SEPARATOR
				. rtrim($contextItem, '.')
				. '.xml',
			);
			$item->addChild('lastmod', (new \DateTime())->format('Y-m-d'));
		}
		return file_put_contents(Page::getSitemapsDirectory() . '/sitemap.xml', $sitemap->asXML());
	}

	private function generateByName(string $name, array $pages) {
		$sitemap = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');
		/** @var Page $page */
		foreach ($pages as $page) {
			$item = $sitemap->addChild("url");
			$item->addChild("loc", $this->getProtocol()
				. ($_SERVER['SERVER_NAME'] ?? $_ENV['SERVER_NAME'] ?? "localhost") . DIRECTORY_SEPARATOR
				. 'sitemaps'. DIRECTORY_SEPARATOR
				. $page->getUri());
			$upper = mb_strtoupper($name);
			$priority = "SITEMAP_NAME_" . $upper . "_PRIORITY";
			$item->addChild('priority', $_ENV[$priority]);
			$item->addChild('lastmod', $page->getUpdatedAt()->format('Y-m-d'));
			$frequency = "SITEMAP_NAME_" . $upper . "_FREQUENCY";
			$item->addChild('changefreq', $_ENV[$frequency]);

		}
		return file_put_contents(Page::getSitemapsDirectory() . '/' . rtrim($name, '.') . '.xml', $sitemap->asXML());
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
	/**
	 * Removes absent maps
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	private function removeAbsent(OutputInterface $output) {

		foreach (glob($this->appKernel->getProjectDir().'/public/sitemaps/*') as $path) {

			if (!preg_match('#/public/sitemaps/(.+).xml#', $path, $out)) {
				continue;
			}

			$name = $out[1];

			if (is_string($name)
				&& $name != 'sitemap'
				&& !in_array($name, $this->sitemapNames)) {
				unlink($path);

				$output->writeln('Файл '. $path .' удален');
			}

		}
	}
}
