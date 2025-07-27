<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Category;

/**
 */
class CategoryServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Category\CategoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetCategory(\Category\CategoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/category.CategoryService/GetCategory',
        $argument,
        ['\Category\CategoryResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Category\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListCategories(\Category\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/category.CategoryService/ListCategories',
        $argument,
        ['\Category\CategoriesResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Category\CreateCategoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateCategory(\Category\CreateCategoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/category.CategoryService/CreateCategory',
        $argument,
        ['\Category\CategoryResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Category\UpdateCategoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UpdateCategory(\Category\UpdateCategoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/category.CategoryService/UpdateCategory',
        $argument,
        ['\Category\CategoryResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Category\CategoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteCategory(\Category\CategoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/category.CategoryService/DeleteCategory',
        $argument,
        ['\Category\DeleteResponse', 'decode'],
        $metadata, $options);
    }

}
