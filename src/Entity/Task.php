<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *  attributes={"security" = "is_granted('ROLE_USER')"},
 *  normalizationContext={"groups"={"Task:read"}},
 *  denormalizationContext={"groups"={"Task:write"}},
 *  itemOperations={
 *      "get"={"security"="object.getUser() == user"},
 *      "delete"={"security"="object.getUser() == user"},
 *      "patch" = {"groups"={"Task:patch"}, "security"="object.getUser() == user"}
 *  },
 *  collectionOperations={
 *     "get",
 *     "post"
 *  },
 * )
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Task:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"Task:read", "Task:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Task:read", "Task:write"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"Task:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"Task:read", "Task:patch"})
     */
    private $completedAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Task:read", "Task:patch"})
     */
    private $completed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     */
    private $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->completedAt = null;
        $this->completed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        if($completed == true && $this->completed == false){
            $this->completed = true;
            $this->completedAt = new \DateTime();
        } else if ($completed == false && $this->completed == true){
            $this->completed = false;
            $this->completedAt = null;
        } else {
            $errorMessage = $this->completed ? "completed" : "uncompleted";
            throw new \Exception("The task is already marked as " . $errorMessage);
        }

        return $this;
    }
}
