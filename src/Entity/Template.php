<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $view = null;

    #[ORM\Column(length: 255)]
    private ?string $context = null;

    /**
     * @var Collection<int, Page>
     */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'template', orphanRemoval: true)]
    private Collection $page;

    public function __construct()
    {
        $this->page = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function setView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPage(): Collection
    {
        return $this->page;
    }

    public function addPage(Page $page): static
    {
        if (!$this->page->contains($page)) {
            $this->page->add($page);
            $page->setTemplate($this);
        }

        return $this;
    }

    public function removePage(Page $page): static
    {
        if ($this->page->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getTemplate() === $this) {
                $page->setTemplate(null);
            }
        }

        return $this;
    }
}
