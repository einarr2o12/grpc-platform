package main

import (
	"log"
	"net/http"
	"os"

	"go-api-gateway/internal/controllers"
	"go-api-gateway/internal/services"
	
	"github.com/gin-gonic/gin"
)

func main() {
	// Initialize services
	categoryService, err := services.NewCategoryService()
	if err != nil {
		log.Fatalf("Failed to initialize category service: %v", err)
	}
	defer categoryService.Close()

	productService, err := services.NewProductService()
	if err != nil {
		log.Fatalf("Failed to initialize product service: %v", err)
	}
	defer productService.Close()

	// Initialize controllers
	categoryController := controllers.NewCategoryController(categoryService)
	productController := controllers.NewProductController(productService, categoryService)

	// Setup Gin router
	router := gin.Default()

	// Add CORS middleware
	router.Use(func(c *gin.Context) {
		c.Writer.Header().Set("Access-Control-Allow-Origin", "*")
		c.Writer.Header().Set("Access-Control-Allow-Credentials", "true")
		c.Writer.Header().Set("Access-Control-Allow-Headers", "Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization, accept, origin, Cache-Control, X-Requested-With")
		c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS, GET, PUT, DELETE")

		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}

		c.Next()
	})

	// Health check endpoint
	router.GET("/health", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{
			"status":    "healthy",
			"service":   "go-api-gateway",
			"timestamp": "2024-07-01T00:00:00Z",
		})
	})

	// API routes
	api := router.Group("/api")
	{
		// Category routes
		categories := api.Group("/categories")
		{
			categories.GET("", categoryController.GetCategories)
			categories.POST("", categoryController.CreateCategory)
			categories.GET("/:id", categoryController.GetCategory)
			categories.PUT("/:id", categoryController.UpdateCategory)
			categories.DELETE("/:id", categoryController.DeleteCategory)
		}

		// Product routes
		products := api.Group("/products")
		{
			products.GET("", productController.GetProducts)
			products.POST("", productController.CreateProduct)
			products.GET("/:id", productController.GetProduct)
			products.PUT("/:id", productController.UpdateProduct)
			products.DELETE("/:id", productController.DeleteProduct)
			products.GET("/category/:categoryId", productController.GetProductsByCategory)
		}

		// Service aggregation route (demonstrates calling multiple gRPC services)
		api.GET("/products-with-category/:categoryId", productController.GetProductsWithCategoryDetails)
	}

	// Get port from environment or use default
	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	log.Printf("🚀 Go API Gateway starting on port %s", port)
	log.Printf("📋 Available endpoints:")
	log.Printf("   GET    /health")
	log.Printf("   GET    /api/categories")
	log.Printf("   POST   /api/categories")
	log.Printf("   GET    /api/categories/:id")
	log.Printf("   PUT    /api/categories/:id")
	log.Printf("   DELETE /api/categories/:id")
	log.Printf("   GET    /api/products")
	log.Printf("   POST   /api/products")
	log.Printf("   GET    /api/products/:id")
	log.Printf("   PUT    /api/products/:id")
	log.Printf("   DELETE /api/products/:id")
	log.Printf("   GET    /api/products/category/:categoryId")
	log.Printf("   GET    /api/products-with-category/:categoryId")

	// Start server
	if err := router.Run(":" + port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}