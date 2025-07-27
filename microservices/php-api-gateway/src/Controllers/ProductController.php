<?php

namespace App\Controllers;

use App\Services\ProductService;
use App\Services\CategoryService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class ProductController
{
    private ProductService $productService;
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $products = $this->productService->listProducts();
            return $this->jsonResponse(['products' => $products]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        try {
            $product = $this->productService->getProduct($id);
            return $this->jsonResponse($product);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['name']) || !isset($data['description']) || 
            !isset($data['price']) || !isset($data['category_id'])) {
            return $this->errorResponse('Name, description, price, and category_id are required', 400);
        }

        try {
            $product = $this->productService->createProduct($data);
            return $this->jsonResponse($product, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['name']) || !isset($data['description']) || 
            !isset($data['price']) || !isset($data['category_id'])) {
            return $this->errorResponse('Name, description, price, and category_id are required', 400);
        }

        try {
            $product = $this->productService->updateProduct($id, $data);
            return $this->jsonResponse($product);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        try {
            $result = $this->productService->deleteProduct($id);
            return $this->jsonResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function getByCategory(ServerRequestInterface $request): ResponseInterface
    {
        $categoryId = $request->getAttribute('categoryId');
        
        try {
            $products = $this->productService->getProductsByCategory($categoryId);
            return $this->jsonResponse(['products' => $products]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function getWithCategoryDetails(ServerRequestInterface $request): ResponseInterface
    {
        $categoryId = $request->getAttribute('categoryId');
        
        try {
            // Get category details via gRPC
            $category = $this->categoryService->getCategory($categoryId);
            
            // Get products for this category via gRPC
            $products = $this->productService->getProductsByCategory($categoryId);
            
            return $this->jsonResponse([
                'category' => $category,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    private function jsonResponse(array $data, int $status = 200): ResponseInterface
    {
        $response = new Response($status);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function errorResponse(string $message, int $status = 400): ResponseInterface
    {
        return $this->jsonResponse(['error' => $message], $status);
    }
}