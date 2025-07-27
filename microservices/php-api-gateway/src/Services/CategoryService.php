<?php

namespace App\Services;

use Grpc\ChannelCredentials;

class CategoryService
{
    private $client;

    public function __construct()
    {
        $hostname = $_ENV['CATEGORY_SERVICE_URL'] ?? 'category-service:50051';
        $this->client = new \Category\CategoryServiceClient($hostname, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);
    }

    public function getCategory(string $id): array
    {
        $request = new \Category\CategoryRequest();
        $request->setId($id);

        list($response, $status) = $this->client->GetCategory($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Category not found', 404);
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription()
        ];
    }

    public function listCategories(): array
    {
        $request = new \Category\Empty();
        list($response, $status) = $this->client->ListCategories($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to list categories');
        }

        $categories = [];
        foreach ($response->getCategories() as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription()
            ];
        }

        return $categories;
    }

    public function createCategory(array $data): array
    {
        $request = new \Category\CreateCategoryRequest();
        $request->setName($data['name']);
        $request->setDescription($data['description']);

        list($response, $status) = $this->client->CreateCategory($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to create category');
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription()
        ];
    }

    public function updateCategory(string $id, array $data): array
    {
        $request = new \Category\UpdateCategoryRequest();
        $request->setId($id);
        $request->setName($data['name']);
        $request->setDescription($data['description']);

        list($response, $status) = $this->client->UpdateCategory($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to update category', 404);
        }

        return [
            'id' => $response->getId(),
            'name' => $response->getName(),
            'description' => $response->getDescription()
        ];
    }

    public function deleteCategory(string $id): array
    {
        $request = new \Category\CategoryRequest();
        $request->setId($id);

        list($response, $status) = $this->client->DeleteCategory($request)->wait();
        
        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \Exception('Failed to delete category', 404);
        }

        return [
            'success' => $response->getSuccess(),
            'message' => $response->getMessage()
        ];
    }
}