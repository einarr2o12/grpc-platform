<?php

namespace App\Services;

use Grpc\ChannelCredentials;

class ProductService
{
    private $client;

    public function __construct()
    {
        $hostname = $_ENV['PRODUCT_SERVICE_URL'] ?? 'product-service:50052';
        $this->client = new \Product\ProductServiceClient($hostname, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);
    }

    public function getProduct(string $id): array
    {
        $request = new \Product\ProductRequest();
        $request->setId($id);

        list($response, $status) = $this->client->GetProduct($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Product not found', 404);
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription(),
            'price' => $response->getPrice(),
            'category_id' => $response->getCategoryId()
        ];
    }

    public function listProducts(): array
    {
        $request = new \Product\Empty();
        list($response, $status) = $this->client->ListProducts($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to list products');
        }

        $products = [];
        foreach ($response->getProducts() as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'category_id' => $product->getCategoryId()
            ];
        }

        return $products;
    }

    public function getProductsByCategory(string $categoryId): array
    {
        $request = new \Product\CategoryRequest();
        $request->setCategoryId($categoryId);

        list($response, $status) = $this->client->GetProductsByCategory($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to get products by category');
        }

        $products = [];
        foreach ($response->getProducts() as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'category_id' => $product->getCategoryId()
            ];
        }

        return $products;
    }

    public function createProduct(array $data): array
    {
        $request = new \Product\CreateProductRequest();
        $request->setName($data['name']);
        $request->setDescription($data['description']);
        $request->setPrice($data['price']);
        $request->setCategoryId($data['category_id']);

        list($response, $status) = $this->client->CreateProduct($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to create product');
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription(),
            'price' => $response->getPrice(),
            'category_id' => $response->getCategoryId()
        ];
    }

    public function updateProduct(string $id, array $data): array
    {
        $request = new \Product\UpdateProductRequest();
        $request->setId($id);
        $request->setName($data['name']);
        $request->setDescription($data['description']);
        $request->setPrice($data['price']);
        $request->setCategoryId($data['category_id']);

        list($response, $status) = $this->client->UpdateProduct($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to update product', 404);
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription(),
            'price' => $response->getPrice(),
            'category_id' => $response->getCategoryId()
        ];
    }

    public function deleteProduct(string $id): array
    {
        $request = new \Product\ProductRequest();
        $request->setId($id);

        list($response, $status) = $this->client->DeleteProduct($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to delete product', 404);
        }

        return [
            'success' => $response->getSuccess(),
            'message' => $response->getMessage()
        ];
    }
}