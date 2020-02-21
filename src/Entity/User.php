<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="user")
     */
    private $invoices;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ape;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bic;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $banque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sepa;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_signature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ref;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $offre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $htlocam;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dtfinlocam;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ht;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_portabilite;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;



    public function __construct()
    {
        parent::__construct();
        $this->tariff = new ArrayCollection();
        $this->product = new ArrayCollection();
        $this->page = new ArrayCollection();
        $this->invoices = new ArrayCollection();
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

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setUser($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getUser() === $this) {
                $invoice->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }


    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getApe(): ?string
    {
        return $this->ape;
    }

    public function setApe(?string $ape): self
    {
        $this->ape = $ape;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    public function getBanque(): ?string
    {
        return $this->banque;
    }

    public function setBanque(?string $banque): self
    {
        $this->banque = $banque;

        return $this;
    }

    public function getSepa(): ?string
    {
        return $this->sepa;
    }

    public function setSepa(?string $sepa): self
    {
        $this->sepa = $sepa;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->date_signature;
    }

    public function setDateSignature(?\DateTimeInterface $date_signature): self
    {
        $this->date_signature = $date_signature;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getOffre(): ?string
    {
        return $this->offre;
    }

    public function setOffre(?string $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getHTLOCAM(): ?string
    {
        return $this->htlocam;
    }

    public function setHtlocam(?string $htlocam): self
    {
        $this->htlocam = $htlocam;

        return $this;
    }

    public function getDtfinlocam(): ?string
    {
        return $this->dtfinlocam;
    }

    public function setDtfinlocam(?string $dtfinlocam): self
    {
        $this->dtfinlocam = $dtfinlocam;

        return $this;
    }

    public function getHt(): ?string
    {
        return $this->ht;
    }

    public function setHt(?string $ht): self
    {
        $this->ht = $ht;

        return $this;
    }

    public function getDatePortabilite(): ?\DateTimeInterface
    {
        return $this->date_portabilite;
    }

    public function setDatePortabilite(?\DateTimeInterface $date_portabilite): self
    {
        $this->date_portabilite = $date_portabilite;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }


}