<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    const PICTURE_PATH = 'picture/post/';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private $author;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'posts')]
    private $game;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 100)]
    private $picturePath;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\File(maxSize: '1024k',
        mimeTypes: ['image/png','image/jpg'],
        mimeTypesMessage: 'Utilisez une image au format .png ou .jpg',
        groups: ['create'],
    )]
    private $pictureDwlFile;

    #[ORM\Column(type:'datetime_immutable')]
    #[Gedmo\Timestampable(on:"create")]
    private $createdAt;

    #[ORM\Column(type:'datetime_immutable', nullable: true)]
    #[Gedmo\Timestampable(on:"update")]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPictureDwlFile(): mixed
    {
        return $this->pictureDwlFile;
    }

    public function setPictureDwlFile(mixed $pictureDwlFile): Post
    {
        $this->pictureDwlFile = $pictureDwlFile;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtString(): string
    {
        return $this->getCreatedAt()->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedAtString(): string
    {
        return $this->getUpdatedAt()->format('Y-m-d H:i:s');
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
