<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Model;

use JulioBarbosa\BombardierPopulateCatalog\Api\ProductManagementInterface;
use JulioBarbosa\BombardierPopulateCatalog\Model\Api\Client;
use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CronException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Registry;
use function __;
use function array_rand;
use function reset;

class ProductManagement implements ProductManagementInterface
{
    public const URL = "https://fakestoreapi.com/products";
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;
    /**
     * @var AttributeSetRepositoryInterface
     */
    private AttributeSetRepositoryInterface $attributeSetRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param Client $client
     * @param ProductFactory $productFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Registry $registry
     */
    public function __construct(
        Client                          $client,
        ProductFactory                  $productFactory,
        CategoryCollectionFactory       $categoryCollectionFactory,
        CategoryRepositoryInterface     $categoryRepository,
        ProductRepositoryInterface      $productRepository,
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        Registry                        $registry
    ) {
        $this->client = $client;
        $this->productFactory = $productFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $registry->register('isSecureArea', true);
    }

    /**
     * Fetch And Process Products
     *
     * @return array
     * @throws CronException
     */
    public function fetchAndProcessProducts(): array
    {
        try {
            $products = $this->client->fetchData(self::URL);

            if ($products) {
                return $this->createProducts($products);
            }
        } catch (Exception $exception) {
            throw new CronException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * Create Products
     *
     * @param array $products
     * @return array
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function createProducts(array $products): array
    {
        $productsReturned = [];
        $categories = $this->categoryCollectionFactory->create()->getItems();
        $attributeId = $this->getDefaultAttributeSetId();

        foreach ($products as $productData) {
            $this->deleteProduct((string)$productData['id']);
            $product = $this->productFactory->create();
            $product->setName($productData['title'])
                ->setSku((string)$productData['id'])
                ->setPrice($productData['price'])
                ->setTypeId(Type::TYPE_SIMPLE)
                ->setAttributeSetId($attributeId)
                ->setDescription($productData['description'])
                ->setVisibility(Visibility::VISIBILITY_BOTH)
                ->setStatus(Status::STATUS_ENABLED);

            if (!empty($categories)) {
                $randomCategory = $categories[array_rand($categories)];
                $product->setCategoryIds([$randomCategory->getId()]);
                $product->setCategoryId($randomCategory->getId());
            }
            $this->productRepository->save($product);
            $productsReturned[] = $product->getData();
        }

        return $productsReturned;
    }

    /**
     * Felete Product
     *
     * @param string $sku
     * @return void
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function deleteProduct(string $sku): void
    {
        $product = $this->productRepository->get($sku);
        $this->productRepository->delete($product);
    }

    /**
     * Get Default Attribute Set Id
     *
     * @return int|null
     */
    public function getDefaultAttributeSetId()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('attribute_set_name', 'Default')
            ->addFilter('entity_type_id', 4)
            ->create();
        $attributeSetList = $this->attributeSetRepository->getList($searchCriteria);
        $items = $attributeSetList->getItems();

        if (!empty($items)) {
            return (int)reset($items)->getAttributeSetId();
        }
        return null;
    }
}
