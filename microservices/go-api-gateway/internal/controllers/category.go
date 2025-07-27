package controllers

import (
	"net/http"
	"go-api-gateway/internal/services"
	"github.com/gin-gonic/gin"
)

// CategoryController handles HTTP requests for categories
type CategoryController struct {
	categoryService *services.CategoryService
}

// NewCategoryController creates a new category controller
func NewCategoryController(categoryService *services.CategoryService) *CategoryController {
	return &CategoryController{
		categoryService: categoryService,
	}
}

// GetCategories handles GET /api/categories
func (c *CategoryController) GetCategories(ctx *gin.Context) {
	categories, err := c.categoryService.ListCategories(ctx.Request.Context())
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, gin.H{"categories": categories})
}

// GetCategory handles GET /api/categories/:id
func (c *CategoryController) GetCategory(ctx *gin.Context) {
	id := ctx.Param("id")
	
	category, err := c.categoryService.GetCategory(ctx.Request.Context(), id)
	if err != nil {
		ctx.JSON(http.StatusNotFound, gin.H{"error": "Category not found"})
		return
	}

	ctx.JSON(http.StatusOK, category)
}

// CreateCategory handles POST /api/categories
func (c *CategoryController) CreateCategory(ctx *gin.Context) {
	var req services.CreateCategoryRequest
	if err := ctx.ShouldBindJSON(&req); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	category, err := c.categoryService.CreateCategory(ctx.Request.Context(), &req)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusCreated, category)
}

// UpdateCategory handles PUT /api/categories/:id
func (c *CategoryController) UpdateCategory(ctx *gin.Context) {
	id := ctx.Param("id")
	
	var req services.CreateCategoryRequest
	if err := ctx.ShouldBindJSON(&req); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	category, err := c.categoryService.UpdateCategory(ctx.Request.Context(), id, &req)
	if err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, category)
}

// DeleteCategory handles DELETE /api/categories/:id
func (c *CategoryController) DeleteCategory(ctx *gin.Context) {
	id := ctx.Param("id")
	
	if err := c.categoryService.DeleteCategory(ctx.Request.Context(), id); err != nil {
		ctx.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	ctx.JSON(http.StatusOK, gin.H{
		"success": true,
		"message": "Category deleted successfully",
	})
}