<?php

namespace Intaro\BookStoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Intaro\BookStoreBundle\Repository\BookRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Book
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="cover", type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @Assert\File(maxSize = "5M", mimeTypes={"image/jpg", "image/jpeg", "image/png"})
     */
    private $cover_src;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @Assert\File(maxSize = "5M")
     */
    private $file_src;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reading_date", type="datetime")
     */
    private $readingDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="download", type="smallint")
     */
    private $download;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Book
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set cover
     *
     * @param string $cover
     *
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Book
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set readingDate
     *
     * @param \DateTime $readingDate
     *
     * @return Book
     */
    public function setReadingDate($readingDate)
    {
        $this->readingDate = $readingDate;

        return $this;
    }

    /**
     * Get readingDate
     *
     * @return \DateTime
     */
    public function getReadingDate()
    {
        return $this->readingDate;
    }

    /**
     * Set download
     *
     * @param integer $download
     *
     * @return Book
     */
    public function setDownload($download)
    {
        $this->download = (int)$download;

        return $this;
    }

    /**
     * Get download
     *
     * @return integer
     */
    public function getDownload()
    {
        return (boolean)$this->download;
    }


    /**
     * Sets cover source.
     *
     * @param UploadedFile $cover
     */
    public function setCoverSrc(UploadedFile $cover = null)
    {
        $this->cover_src = $cover;
    }

    /**
     * Get cover source.
     *
     * @return UploadedFile
     */
    public function getCoverSrc()
    {
        return $this->cover_src;
    }

    /**
     * Sets file source.
     *
     * @param UploadedFile $file
     */
    public function setFileSrc(UploadedFile $file = null)
    {
        $this->file_src = $file;
    }

    /**
     * Get file source.
     *
     * @return UploadedFile
     */
    public function getFileSrc()
    {
        return $this->file_src;
    }

    public function getAbsolutePath($path)
    {
        return $this->$path === null
            ? null
            : $this->getUploadRootDir($path).'/'.$this->$path;
    }

    public function getWebPath($path)
    {
        return null === $this->$path
            ? null
            : $this->getUploadDir($path).'/'.$this->$path;
    }

    protected function getUploadRootDir($path)
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir($path);
    }

    protected function getUploadDir($path)
    {
        return '/uploads/' . $path . 's';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $items = ['cover', 'file'];

        foreach ($items as $path) {
            $pathSrc = $path . '_src';

            if ($this->$pathSrc !== null) {
                $filename = sha1(uniqid(mt_rand(), true));
                $this->$path = date('mY')
                    . '/' . $filename . '.' . $this->$pathSrc->guessExtension();
            }
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        $items = ['cover', 'file'];

        foreach ($items as $path) {
            $pathSrc = $path . '_src';

            if ($this->$pathSrc === null) {
                return;
            }

            $namePieces = explode('/', $this->$path);

            $this->$pathSrc->move(
                $this->getUploadRootDir($path) . '/' . $namePieces[0],
                $namePieces[1]
            );

            $this->$pathSrc = null;
        }
    }

    public function removeFile($type)
    {
        $src = $this->getAbsolutePath($type);

        if ($src && file_exists($src)) {
            unlink($src);
        }

        if ($this->$type) {
            $this->$type = null;
        }
    }
}
