<?php

namespace Domain\Model\User;

use DateTime;
use Domain\Model\Event\UserRegisteredEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Embedded(class: UserId::class)]
    protected UserId $id;

    #[ORM\Embedded(class: Name::class)]
    private Name $name;

    #[ORM\Embedded(class: Email::class)]
    private Email $email;

    #[ORM\Embedded(class: Password::class)]
    private Password $password;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    private array $events = [];

    private function __construct(
        UserId $id,
        Name $name,
        Email $email,
        Password $password,
        DateTime $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public static function register(UserId $id, Name $name, Email $email, Password $password): self
    {
        $user = new self(
            $id,
            $name,
            $email,
            $password,
            new DateTime()
        );

        $user->recordEvent(new UserRegisteredEvent($user));

        return $user;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    private function recordEvent($event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
