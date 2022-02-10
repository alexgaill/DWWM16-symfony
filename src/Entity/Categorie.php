<?php
namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(CategorieRepository::class),
    UniqueEntity('name', message:"Une catégorie existe déjà avec ce nom")
]
class Categorie {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[
        ORM\Column(type:"string", length:50),
        Assert\Length(
            min: 10,
            minMessage: "La catégorie doit contenir au minimum {{ limit }} caractères."
        ),
        Assert\NotBlank(
            message:"Le nom de la catégorie ne peut être vide."
        )
    ]
    private string $name;

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}