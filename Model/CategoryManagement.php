<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Model;

use JulioBarbosa\BombardierPopulateCatalog\Api\CategoryManagementInterface;
use JulioBarbosa\BombardierPopulateCatalog\Model\Api\Client;
use Exception;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CronException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use function in_array;
use function __;

class CategoryManagement implements CategoryManagementInterface
{
    public const URL = "https://fakestoreapi.com/products/categories";
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;
    /**
     * @var CategoryLinkManagementInterface
     */
    private CategoryLinkManagementInterface $categoryLinkManagement;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param Client $client
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Client                          $client,
        CategoryFactory                 $categoryFactory,
        CategoryCollectionFactory       $categoryCollectionFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        CategoryRepositoryInterface     $categoryRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->client = $client;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->productRepository = $productRepository;
    }

    /**
     * Fetch And Process Categories
     *
     * @return void
     * @throws LocalizedException
     */
    public function fetchAndProcessCategories(): void
    {
        try {
            $parentId = Category::TREE_ROOT_ID;
            $categories = $this->client->fetchData(self::URL);

            if ($categories) {
                $this->createCategories($parentId, $categories);
            }
        } catch (Exception $exception) {
            throw new CronException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * Create Categories
     *
     * @param int $parentId
     * @param array $categories
     * @return void
     * @throws LocalizedException
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function createCategories(int $parentId, array $categories): void
    {
        // Delete existing categories
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('is_active', 1);
        $collection->addAttributeToSelect('name');

        foreach ($collection as $category) {
            if (in_array($category->getName(), $categories)) {
                $category = $this->categoryRepository->get($category->getId());
                $products = $category->getProductCollection()->getAllIds();

                foreach ($products as $id) {
                    $product = $this->productRepository->getById($id);
                    if (!empty($product)) {
                        $this->categoryLinkManagement->assignProductToCategories(
                            $product->getSku(),
                            [$parentId]
                        );
                    }
                }
                $this->categoryRepository->deleteByIdentifier($category->getId());
            }
        }

        // Create new categories
        foreach ($categories as $categoryName) {
            $newCategory = $this->categoryFactory->create();
            $newCategory->setName($categoryName)
                ->setIsActive(true)
                ->setParentId($parentId)
                ->setIncludeInMenu(true);

            $this->categoryRepository->save($newCategory);
        }
    }
}
