package services

import (
	"context"
	"fmt"
	productPb "go-api-gateway/internal/generated/product"
	"os"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

// ProductService handles gRPC communication with product service
type ProductService struct {
	conn   *grpc.ClientConn
	client productPb.ProductServiceClient
}

// Product represents a product
type Product struct {
	ID          string  `json:"id"`
	Name        string  `json:"name"`
	Description string  `json:"description"`
	Price       float64 `json:"price"`
	CategoryID  string  `json:"category_id"`
}

// CreateProductRequest represents create product request
type CreateProductRequest struct {
	Name        string  `json:"name"`
	Description string  `json:"description"`
	Price       float64 `json:"price"`
	CategoryID  string  `json:"category_id"`
}

// NewProductService creates a new product service
func NewProductService() (*ProductService, error) {
	serviceURL := os.Getenv("PRODUCT_SERVICE_URL")
	if serviceURL == "" {
		serviceURL = "product-service:50052"
	}

	conn, err := grpc.Dial(serviceURL, grpc.WithTransportCredentials(insecure.NewCredentials()))
	if err != nil {
		return nil, fmt.Errorf("failed to connect to product service: %w", err)
	}

	return &ProductService{
		conn:   conn,
		client: productPb.NewProductServiceClient(conn),
	}, nil
}

// Close closes the gRPC connection
func (s *ProductService) Close() error {
	return s.conn.Close()
}

// GetProduct gets a product by ID
func (s *ProductService) GetProduct(ctx context.Context, id string) (*Product, error) {
	req := &productPb.ProductRequest{Id: id}
	resp, err := s.client.GetProduct(ctx, req)
	if err != nil {
		return nil, fmt.Errorf("failed to get product: %w", err)
	}

	return &Product{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
		Price:       resp.Price,
		CategoryID:  resp.CategoryId,
	}, nil
}

// ListProducts lists all products
func (s *ProductService) ListProducts(ctx context.Context) ([]*Product, error) {
	req := &productPb.Empty{}
	resp, err := s.client.ListProducts(ctx, req)
	if err != nil {
		return nil, fmt.Errorf("failed to list products: %w", err)
	}

	products := make([]*Product, len(resp.Products))
	for i, prod := range resp.Products {
		products[i] = &Product{
			ID:          prod.Id,
			Name:        prod.Name,
			Description: prod.Description,
			Price:       prod.Price,
			CategoryID:  prod.CategoryId,
		}
	}
	return products, nil
}

// GetProductsByCategory gets products by category ID
func (s *ProductService) GetProductsByCategory(ctx context.Context, categoryID string) ([]*Product, error) {
	req := &productPb.CategoryRequest{CategoryId: categoryID}
	resp, err := s.client.GetProductsByCategory(ctx, req)
	if err != nil {
		return nil, fmt.Errorf("failed to get products by category: %w", err)
	}

	products := make([]*Product, len(resp.Products))
	for i, prod := range resp.Products {
		products[i] = &Product{
			ID:          prod.Id,
			Name:        prod.Name,
			Description: prod.Description,
			Price:       prod.Price,
			CategoryID:  prod.CategoryId,
		}
	}
	return products, nil
}

// CreateProduct creates a new product
func (s *ProductService) CreateProduct(ctx context.Context, req *CreateProductRequest) (*Product, error) {
	grpcReq := &productPb.CreateProductRequest{
		Name:        req.Name,
		Description: req.Description,
		Price:       req.Price,
		CategoryId:  req.CategoryID,
	}
	resp, err := s.client.CreateProduct(ctx, grpcReq)
	if err != nil {
		return nil, fmt.Errorf("failed to create product: %w", err)
	}

	return &Product{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
		Price:       resp.Price,
		CategoryID:  resp.CategoryId,
	}, nil
}

// UpdateProduct updates an existing product
func (s *ProductService) UpdateProduct(ctx context.Context, id string, req *CreateProductRequest) (*Product, error) {
	grpcReq := &productPb.UpdateProductRequest{
		Id:          id,
		Name:        req.Name,
		Description: req.Description,
		Price:       req.Price,
		CategoryId:  req.CategoryID,
	}
	resp, err := s.client.UpdateProduct(ctx, grpcReq)
	if err != nil {
		return nil, fmt.Errorf("failed to update product: %w", err)
	}

	return &Product{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
		Price:       resp.Price,
		CategoryID:  resp.CategoryId,
	}, nil
}

// DeleteProduct deletes a product
func (s *ProductService) DeleteProduct(ctx context.Context, id string) error {
	req := &productPb.ProductRequest{Id: id}
	_, err := s.client.DeleteProduct(ctx, req)
	if err != nil {
		return fmt.Errorf("failed to delete product: %w", err)
	}
	return nil
}
