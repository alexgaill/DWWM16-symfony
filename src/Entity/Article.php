<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $content;

    private string $subContent;

    
    #[ORM\Column(type: 'datetime', updatable:false)]
    private $createdAt;
    
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private $categorie;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    #[Assert\File(
        mimeTypes:["image/png", "image/jpeg"],
        mimeTypesMessage: "Le format fichier ne correspond pas aux formats attendus"
    )]
    private $picture;
    
    public function __construct()
    {
        $this->subContent = substr($this->content, 0, 50) . "...";
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    /**
     * Get the value of subContent
     *
     * @return string
     */
    public function getSubContent(): string
    {
        return $this->subContent = substr($this->content, 0, 50) . "...";
    }

    /**
     * Set the value of subContent
     *
     * @param string $subContent
     *
     * @return self
     */
    public function setSubContent(string $subContent): self
    {
        $this->subContent = $subContent;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    #[ORM\PostRemove]
    public function deletePicture()
    {
        if (file_exists(__DIR__. '/../../public/upload/'. $this->picture)) {
            unlink(__DIR__. '/../../public/upload/'. $this->picture);
        }
        return true;
    }
}
