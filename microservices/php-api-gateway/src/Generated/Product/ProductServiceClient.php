<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Product;

/**
 */
class ProductServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Product\ProductRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetProduct(\Product\ProductRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/GetProduct',
        $argument,
        ['\Product\ProductResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Product\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListProducts(\Product\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/ListProducts',
        $argument,
        ['\Product\ProductsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Product\CategoryRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetProductsByCategory(\Product\CategoryRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/GetProductsByCategory',
        $argument,
        ['\Product\ProductsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Product\CreateProductRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateProduct(\Product\CreateProductRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/CreateProduct',
        $argument,
        ['\Product\ProductResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Product\UpdateProductRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UpdateProduct(\Product\UpdateProductRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/UpdateProduct',
        $argument,
        ['\Product\ProductResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Product\ProductRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteProduct(\Product\ProductRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/product.ProductService/DeleteProduct',
        $argument,
        ['\Product\DeleteResponse', 'decode'],
        $metadata, $options);
    }

}
