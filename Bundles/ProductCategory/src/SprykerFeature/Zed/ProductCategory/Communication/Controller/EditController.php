<?php

namespace SprykerFeature\Zed\ProductCategory\Communication\Controller;

use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\NodeTransfer;
use Propel\Runtime\Propel;
use SprykerFeature\Zed\Category\Persistence\Propel\SpyCategoryNode;
use SprykerFeature\Zed\ProductCategory\Business\ProductCategoryFacade;
use SprykerFeature\Zed\ProductCategory\ProductCategoryConfig;
use SprykerFeature\Zed\ProductCategory\Communication\ProductCategoryDependencyContainer;
use SprykerFeature\Zed\ProductCategory\Persistence\ProductCategoryQueryContainer;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method ProductCategoryFacade getFacade()
 * @method ProductCategoryDependencyContainer getDependencyContainer()
 * @method ProductCategoryQueryContainer getQueryContainer()
 */
class EditController extends AddController
{
    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idCategory = $request->get(ProductCategoryConfig::PARAM_ID_CATEGORY);

        $categoryExists = $this->getDependencyContainer()
                ->createCategoryQueryContainer()
                ->queryCategoryById($idCategory)->count() > 0;

        if (!$categoryExists) {
            $this->addErrorMessage(sprintf('The category you are trying to edit %s does not exist.', $idCategory));

            return new RedirectResponse('/category');
        }

        $locale = $this->getDependencyContainer()
            ->createCurrentLocale();

        /**
         * @var Form
         */
        $form = $this->getDependencyContainer()
            ->createCategoryFormEdit($idCategory);

        $form->handleRequest();

        if ($form->isValid()) {
            $connection = Propel::getConnection();

            $data = $form->getData();

            $currentCategoryTransfer = $this->updateCategory($locale, $data);
            $currentCategoryNodeTransfer = $this->updateCategoryNode($currentCategoryTransfer, $locale, $data);
            $this->updateProductCategoryMappings($currentCategoryTransfer, $data);

            $parentIdList = $data['extra_parents'];
            foreach ($parentIdList as $parentNodeId) {
                $data['fk_parent_category_node'] = $parentNodeId;
                $data['fk_category'] = $currentCategoryTransfer->getIdCategory();

                $this->updateCategoryNodeChild($currentCategoryTransfer, $locale, $data);
            }

            //TODO column was removed from DB, fix later when working on produt preconfig
            //https://kartenmacherei.atlassian.net/browse/KSP-877
            //$this->updateProductCategoryPreconfig($currentCategoryTransfer, (array) json_decode($data['product_category_preconfig']));
            $this->updateProductOrder($currentCategoryTransfer, (array) json_decode($data['product_order'], true));

            $parentIdList[] = $currentCategoryNodeTransfer->getFkParentCategoryNode();
            $parentIdList = array_flip($parentIdList);
            $this->removeDeselectedCategoryAdditionalParents(
                $currentCategoryTransfer,
                $locale,
                $parentIdList
            );

            $this->addSuccessMessage('The category was saved successfully.');

            $connection->commit();

            return $this->redirectResponse('/productCategory/edit?id-category='.$idCategory);
        }

        $productCategories = $this->getDependencyContainer()
            ->createProductCategoryTable($locale, $idCategory);

        $products = $this->getDependencyContainer()
            ->createProductTable($locale, $idCategory);

        return $this->viewResponse([
            'idCategory' => $idCategory,
            'form' => $form->createView(),
            'productCategoriesTable' => $productCategories->render(),
            'productsTable' => $products->render(),
        ]);
    }

    /**
     * @param $existingCategoryNode
     * @param NodeTransfer $categoryNodeTransfer
     * @param LocaleTransfer $locale
     */
    protected function createOrUpdateCategoryNode($existingCategoryNode, NodeTransfer $categoryNodeTransfer, LocaleTransfer $locale)
    {
        /**
         * @var SpyCategoryNode $existingCategoryNode
         */
        if ($existingCategoryNode) {
            $categoryNodeTransfer->setIdCategoryNode($existingCategoryNode->getIdCategoryNode());
            
            $this->getDependencyContainer()
                ->createCategoryFacade()
                ->updateCategoryNode($categoryNodeTransfer, $locale);
        } else {
            $newData = $categoryNodeTransfer->toArray();
            unset($newData['id_category_node']);
            $categoryNodeTransfer = (new NodeTransfer())->fromArray($newData, true);
            
            $this->getDependencyContainer()
                ->createCategoryFacade()
                ->createCategoryNode($categoryNodeTransfer, $locale);
        }
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param LocaleTransfer $locale
     * @param array $parentIdList
     */
    protected function removeDeselectedCategoryAdditionalParents(
        CategoryTransfer $categoryTransfer,
        LocaleTransfer $locale,
        array $parentIdList
    )
    {
        $existingParents = $this->getDependencyContainer()
            ->createCategoryFacade()
            ->getNotMainNodesByIdCategory($categoryTransfer->getIdCategory());

        foreach ($existingParents as $parent) {
            if (!array_key_exists($parent->getFkParentCategoryNode(), $parentIdList)) {
                $this->getDependencyContainer()
                    ->createCategoryFacade()
                    ->deleteNode($parent->getIdCategoryNode(), $locale);
            }
        }
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param array $data
     */
    protected function updateProductCategoryMappings(CategoryTransfer $categoryTransfer, array $data)
    {
        $addProductsMappingCollection = [];
        $removeProductMappingCollection = [];
        if (trim($data['products_to_be_assigned']) !== '') {
            $addProductsMappingCollection = explode(',', $data['products_to_be_assigned']);
        }

        if (trim($data['products_to_be_de_assigned']) !== '') {
            $removeProductMappingCollection = explode(',', $data['products_to_be_de_assigned']);
        }

        if (!empty($removeProductMappingCollection)) {
            $this->getDependencyContainer()
                ->createProductCategoryFacade()
                ->removeProductCategoryMappings($categoryTransfer->getIdCategory(), $removeProductMappingCollection);
        }

        if (!empty($addProductsMappingCollection)) {
            $this->getDependencyContainer()
                ->createProductCategoryFacade()
                ->createProductCategoryMappings($categoryTransfer->getIdCategory(), $addProductsMappingCollection);
        }
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param $productOrder
     */
    protected function updateProductOrder(CategoryTransfer $categoryTransfer, array $productOrder)
    {
        $this->getDependencyContainer()
            ->createProductCategoryFacade()
            ->updateProductMappingsOrder($categoryTransfer->getIdCategory(), $productOrder);
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param $productPreconfig
     */
    protected function updateProductCategoryPreconfig(CategoryTransfer $categoryTransfer, array $productPreconfig)
    {
        $this->getDependencyContainer()
            ->createProductCategoryFacade()
            ->updateProductCategoryPreconfig($categoryTransfer->getIdCategory(), $productPreconfig);
    }

    /**
     * @param LocaleTransfer $locale
     * @param array $data
     *
     * @return CategoryTransfer
     */
    protected function updateCategory(LocaleTransfer $locale, array $data)
    {
        $currentCategoryTransfer = (new CategoryTransfer())
            ->fromArray($data, true);

        $this->getDependencyContainer()
            ->createCategoryFacade()
            ->updateCategory($currentCategoryTransfer, $locale);

        return $currentCategoryTransfer;
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param LocaleTransfer $locale
     * @param array $data
     *
     * @return NodeTransfer
     */
    protected function updateCategoryNode(CategoryTransfer $categoryTransfer, LocaleTransfer $locale, array $data)
    {
        $currentCategoryNodeTransfer = (new NodeTransfer())
            ->fromArray($data, true);

        $currentCategoryNodeTransfer->setIsMain(true);

        /**
         * @var SpyCategoryNode $currentCategoryNode
         */
        $existingCategoryNode = $this->getDependencyContainer()
            ->createCategoryFacade()
            ->getNodeByIdCategoryAndParentNode($categoryTransfer->getIdCategory(), $currentCategoryNodeTransfer->getFkParentCategoryNode());

        $this->createOrUpdateCategoryNode($existingCategoryNode, $currentCategoryNodeTransfer, $locale);

        return $currentCategoryNodeTransfer;
    }

    /**
     * @param CategoryTransfer $categoryTransfer
     * @param LocaleTransfer $locale
     * @param array $data
     *
     * @return NodeTransfer
     */
    protected function updateCategoryNodeChild(CategoryTransfer $categoryTransfer, LocaleTransfer $locale, array $data)
    {
        $nodeTransfer = (new NodeTransfer())
            ->fromArray($data, true);

        $nodeTransfer->setIsMain(false);

        $existingCategoryNode = $this->getDependencyContainer()
            ->createCategoryFacade()
            ->getNodeByIdCategoryAndParentNode($categoryTransfer->getIdCategory(), $nodeTransfer->getFkParentCategoryNode());

        $this->createOrUpdateCategoryNode($existingCategoryNode, $nodeTransfer, $locale);

        return $nodeTransfer;
    }

}