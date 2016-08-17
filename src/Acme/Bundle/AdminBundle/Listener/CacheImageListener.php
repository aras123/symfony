<?php
namespace Acme\Bundle\AdminBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Delete Cache Image where is: $entity->file_image
 */
class CacheImageListener
{
    protected $cacheManager;
    public function __construct($cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        

        if (isset($entity->old_logo) AND $entity->old_logo!='') { //for logo - BrandDevice | BrandFurniture | Company
            $this->cacheManager->remove($entity->getWebPath($entity->old_logo));
        }
        if($entity instanceof \Acme\Bundle\AdminBundle\Entity\StudioImage) { //for image - StudioImage
            if (isset($entity->old_image) AND $entity->old_image!='') {
                $this->cacheManager->remove($entity->getWebPath($entity->old_image));
            }
        }
        if($entity instanceof \Acme\Bundle\AdminBundle\Entity\Expositions) { //for photo - Expositions
            if (isset($entity->old_photo) AND $entity->old_photo!='') {
                $this->cacheManager->remove($entity->getWebPath($entity->old_photo));
            }
        }
        if($entity instanceof \Acme\Bundle\AdminBundle\Entity\Studio) { //for thumbnail - Studio
            if (isset($entity->old_thumbnail) AND $entity->old_thumbnail!='') {
                $this->cacheManager->remove($entity->getWebPath($entity->old_thumbnail));
            }
        }
        
        
    }
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $isLogo = isset($entity->logo) AND $entity->getLogo()!=''; //
        $isStudioImage = $entity instanceof \Acme\Bundle\AdminBundle\Entity\StudioImage;
        $isExpositions = $entity instanceof \Acme\Bundle\AdminBundle\Entity\Expositions;
        $isStudio = $entity instanceof \Acme\Bundle\AdminBundle\Entity\Studio;

        if($isLogo || $isStudioImage || $isExpositions || $isStudio) {
            $this->cacheManager->remove($entity->getWebPath());
        }
    }
}