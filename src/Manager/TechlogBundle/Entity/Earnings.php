<?php

namespace Manager\TechlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Earnings
 *
 * @ORM\Table(name="earnings")
 * @ORM\Entity
 */
class Earnings
{
    /**
     * @var integer
     *
     * @ORM\Column(name="earnings_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $earningsId;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=7, nullable=false)
     */
    private $month;

    /**
     * @var float
     *
     * @ORM\Column(name="expend", type="float", nullable=false)
     */
    private $expend;

    /**
     * @var float
     *
     * @ORM\Column(name="income", type="float", nullable=false)
     */
    private $income;

    /**
     * @var string
     *
     * @ORM\Column(name="inserttime", type="string", nullable=false)
     */
    private $inserttime;

    /**
     * @var integer
     *
     * @ORM\Column(name="article_id", type="integer", nullable=false)
     */
    private $articleId;

    /**
     * @var \Images
     *
     * @ORM\ManyToOne(targetEntity="Images")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="image_id")
     * })
     */
    private $image;


}
