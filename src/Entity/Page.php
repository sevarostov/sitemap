<?php

namespace App\Entity;

use App\Repository\PageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $uri;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'page')]
    #[ORM\JoinColumn(nullable: false)]
    private Template $template;

	/**
	 * @param string $uri
	 * @param Template $template
	 */
	public function __construct(
		string $uri,
		Template $template,
	)
	{
		$this->uri = $uri;
		$this->createdAt = new \DateTimeImmutable('now');
		$this->template = $template;
	}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): static
    {
        $this->template = $template;

        return $this;
    }

	/**
	 * @return string
	 */
	public static function getSitemapsDirectory() {
		return 'public/sitemaps';
	}
}
