<?php

namespace Expresso\ExpressoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="expresso_admin_menu_item")
 */
class AdminMenuItem
{
    /**
     * @param \Expresso\ExpressoBundle\Entity\adminMenuGroup $adminMenuGroup
     */
    public function setAdminMenuGroup($adminMenuGroup)
    {
        $this->adminMenuGroup = $adminMenuGroup;
    }

    /**
     * @return \Expresso\ExpressoBundle\Entity\adminMenuGroup
     */
    public function getAdminMenuGroup()
    {
        return $this->adminMenuGroup;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
    * @var adminMenuGroup $adminMenuGroup
    * @ORM\ManyToOne(targetEntity="Expresso\ExpressoBundle\Entity\AdminMenuGroup")
    * @ORM\JoinColumn(name="admin_menu_group_id", referencedColumnName="id", nullable=FALSE)
    */
    protected $adminMenuGroup;
}