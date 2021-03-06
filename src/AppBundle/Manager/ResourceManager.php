<?php
/**
 * ObjectManager.php
 * restfully
 * Date: 08.04.17
 */

namespace AppBundle\Manager;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ResourceManager
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ResourceManager constructor.
     *
     * @param string             $className
     * @param EntityManager      $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct($className, EntityManager $entityManager, ValidatorInterface $validator)
    {
        $this->className     = $className;
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    /**
     * @param      $model
     * @param null $groups
     * @param null $constraints
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate($model, $groups=null, $constraints = null)
    {
        return $this->validator->validate($model,$constraints, $groups);
    }

    /**
     * @param array $properties
     *
     * @return mixed
     */
    public function createNew($properties = [])
    {
        $class = $this->getRepository()->getClassName();
        $model = new $class;

        return $this->initialize($model, $properties);
    }

    /**
     * @param array|object $models
     * @param bool  $flush
     */
    public function save($models = [], $flush = true)
    {
        if( ! is_array($models) ) {
            $models = [$models];
        }

        $em = $this->getEntityManager();
        foreach($models as $model) {
            $em->persist($model);
        }

        if( $flush ) $em->flush();

    }

    /**
     * @param array|object $models
     * @param bool  $flush
     */
    public function remove($models = [], $flush = true)
    {
        if( ! is_array($models) ) {
            $models = [$models];
        }

        $em = $this->getEntityManager();
        foreach($models as $model) {
            $em->remove($model);
        }

        if( $flush ) $em->flush();
    }


    /**
     * @param       $model
     * @param array $properties
     *
     * @return mixed
     */
    public function initialize($model, $properties = [])
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($properties as $propertyName => $property) {
            $accessor->setValue($model, $propertyName, $property);
        }
        return $model;
    }


    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->className);
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

}