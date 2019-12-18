<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tariff", mappedBy="user")
     */
    private $tariff;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="user")
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Page", mappedBy="user")
     */
    private $page;

    public function __construct()
    {
        parent::__construct();
        $this->tariff = new ArrayCollection();
        $this->product = new ArrayCollection();
        $this->page = new ArrayCollection();
        // your own logic
    }

    /**
     * @return Collection|Tariff[]
     */
    public function getTariff(): Collection
    {
        return $this->tariff;
    }

    public function addTariff(Tariff $tariff): self
    {
        if (!$this->tariff->contains($tariff)) {
            $this->tariff[] = $tariff;
            $tariff->setUser($this);
        }

        return $this;
    }

    public function removeTariff(Tariff $tariff): self
    {
        if ($this->tariff->contains($tariff)) {
            $this->tariff->removeElement($tariff);
            // set the owning side to null (unless already changed)
            if ($tariff->getUser() === $this) {
                $tariff->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->contains($product)) {
            $this->product->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPage(): Collection
    {
        return $this->page;
    }

    public function addPage(Page $page): self
    {
        if (!$this->page->contains($page)) {
            $this->page[] = $page;
            $page->setUser($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->page->contains($page)) {
            $this->page->removeElement($page);
            // set the owning side to null (unless already changed)
            if ($page->getUser() === $this) {
                $page->setUser(null);
            }
        }

        return $this;
    }
}