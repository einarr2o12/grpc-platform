package controllers

import (
	"net/http"
	"go-api-gateway/internal/services"
	"github.com/gin-gonic/gin"
)

// ProductController handles HTTP requests for products
type ProductController struct {
	productService  *services.ProductService
	categoryService *services.CategoryService
}

// NewProductController creates a new product controller
func NewProductController(productService *services.ProductService, categoryService *services.CategoryService) *ProductController {
	return &ProductController{
		productService:  productService,
		categoryService: categoryService,
	}
}

// GetProducts handles GET /api/products
func (c *ProductController) GetProducts(ctx *gin.Context) {
	products, err := c.productService.ListProducts(ctx.Request.Context())
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, gin.H{"products": products})
}

// GetProduct handles GET /api/products/:id
func (c *ProductController) GetProduct(ctx *gin.Context) {
	id := ctx.Param("id")
	
	product, err := c.productService.GetProduct(ctx.Request.Context(), id)
	if err != nil {
		ctx.JSON(http.StatusNotFound, gin.H{"error": "Product not found"})
		return
	}

	ctx.JSON(http.StatusOK, product)
}

// CreateProduct handles POST /api/products
func (c *ProductController) CreateProduct(ctx *gin.Context) {
	var req services.CreateProductRequest
	if err := ctx.ShouldBindJSON(&req); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	product, err := c.productService.CreateProduct(ctx.Request.Context(), &req)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusCreated, product)
}

// UpdateProduct handles PUT /api/products/:id
func (c *ProductController) UpdateProduct(ctx *gin.Context) {
	id := ctx.Param("id")
	
	var req services.CreateProductRequest
	if err := ctx.ShouldBindJSON(&req); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	product, err := c.productService.UpdateProduct(ctx.Request.Context(), id, &req)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, product)
}

// DeleteProduct handles DELETE /api/products/:id
func (c *ProductController) DeleteProduct(ctx *gin.Context) {
	id := ctx.Param("id")
	
	if err := c.productService.DeleteProduct(ctx.Request.Context(), id); err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, gin.H{
		"success": true,
		"message": "Product deleted successfully",
	})
}

// GetProductsByCategory handles GET /api/products/category/:categoryId
func (c *ProductController) GetProductsByCategory(ctx *gin.Context) {
	categoryID := ctx.Param("categoryId")
	
	products, err := c.productService.GetProductsByCategory(ctx.Request.Context(), categoryID)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, gin.H{"products": products})
}

// GetProductsWithCategoryDetails handles GET /api/products-with-category/:categoryId
// This demonstrates service aggregation - calling multiple gRPC services
func (c *ProductController) GetProductsWithCategoryDetails(ctx *gin.Context) {
	categoryID := ctx.Param("categoryId")
	
	// Get category details via gRPC
	category, err := c.categoryService.GetCategory(ctx.Request.Context(), categoryID)
	if err != nil {
		ctx.JSON(http.StatusNotFound, gin.H{"error": "Category not found"})
		return
	}
	
	// Get products for this category via gRPC
	products, err := c.productService.GetProductsByCategory(ctx.Request.Context(), categoryID)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	// Return aggregated data from multiple services
	ctx.JSON(http.StatusOK, gin.H{
		"category": category,
		"products": products,
	})
}