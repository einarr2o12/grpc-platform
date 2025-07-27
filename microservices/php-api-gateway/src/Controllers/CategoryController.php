<?php

namespace App\Controllers;

use App\Services\CategoryService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class CategoryController
{
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $categories = $this->categoryService->listCategories();
            return $this->jsonResponse(['categories' => $categories]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        try {
            $category = $this->categoryService->getCategory($id);
            return $this->jsonResponse($category);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['name']) || !isset($data['description'])) {
            return $this->errorResponse('Name and description are required', 400);
        }

        try {
            $category = $this->categoryService->createCategory($data);
            return $this->jsonResponse($category, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $data = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($data['name']) || !isset($data['description'])) {
            return $this->errorResponse('Name and description are required', 400);
        }

        try {
            $category = $this->categoryService->updateCategory($id, $data);
            return $this->jsonResponse($category);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        try {
            $result = $this->categoryService->deleteCategory($id);
            return $this->jsonResponse($result);
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