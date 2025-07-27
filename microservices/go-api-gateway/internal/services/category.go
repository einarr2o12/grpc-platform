package services

import (
	"context"
	"fmt"
	categoryPb "go-api-gateway/internal/generated/category"
	"os"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

// CategoryService handles gRPC communication with category service
type CategoryService struct {
	conn   *grpc.ClientConn
	client categoryPb.CategoryServiceClient
}

// Category represents a category
type Category struct {
	ID          string `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
}

// CreateCategoryRequest represents create category request
type CreateCategoryRequest struct {
	Name        string `json:"name"`
	Description string `json:"description"`
}

// NewCategoryService creates a new category service
func NewCategoryService() (*CategoryService, error) {
	serviceURL := os.Getenv("CATEGORY_SERVICE_URL")
	if serviceURL == "" {
		serviceURL = "category-service:50051"
	}

	conn, err := grpc.Dial(serviceURL, grpc.WithTransportCredentials(insecure.NewCredentials()))
	if err != nil {
		return nil, fmt.Errorf("failed to connect to category service: %w", err)
	}

	return &CategoryService{
		conn:   conn,
		client: categoryPb.NewCategoryServiceClient(conn),
	}, nil
}

// Close closes the gRPC connection
func (s *CategoryService) Close() error {
	return s.conn.Close()
}

// GetCategory gets a category by ID
func (s *CategoryService) GetCategory(ctx context.Context, id string) (*Category, error) {
	req := &categoryPb.CategoryRequest{Id: id}
	resp, err := s.client.GetCategory(ctx, req)
	if err != nil {
		return nil, fmt.Errorf("failed to get category: %w", err)
	}

	return &Category{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
	}, nil
}

// ListCategories lists all categories
func (s *CategoryService) ListCategories(ctx context.Context) ([]*Category, error) {
	req := &categoryPb.Empty{}
	resp, err := s.client.ListCategories(ctx, req)
	if err != nil {
		return nil, fmt.Errorf("failed to list categories: %w", err)
	}

	categories := make([]*Category, len(resp.Categories))
	for i, cat := range resp.Categories {
		categories[i] = &Category{
			ID:          cat.Id,
			Name:        cat.Name,
			Description: cat.Description,
		}
	}
	return categories, nil
}

// CreateCategory creates a new category
func (s *CategoryService) CreateCategory(ctx context.Context, req *CreateCategoryRequest) (*Category, error) {
	grpcReq := &categoryPb.CreateCategoryRequest{
		Name:        req.Name,
		Description: req.Description,
	}
	resp, err := s.client.CreateCategory(ctx, grpcReq)
	if err != nil {
		return nil, fmt.Errorf("failed to create category: %w", err)
	}

	return &Category{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
	}, nil
}

// UpdateCategory updates an existing category
func (s *CategoryService) UpdateCategory(ctx context.Context, id string, req *CreateCategoryRequest) (*Category, error) {
	grpcReq := &categoryPb.UpdateCategoryRequest{
		Id:          id,
		Name:        req.Name,
		Description: req.Description,
	}
	resp, err := s.client.UpdateCategory(ctx, grpcReq)
	if err != nil {
		return nil, fmt.Errorf("failed to update category: %w", err)
	}

	return &Category{
		ID:          resp.Id,
		Name:        resp.Name,
		Description: resp.Description,
	}, nil
}

// DeleteCategory deletes a category
func (s *CategoryService) DeleteCategory(ctx context.Context, id string) error {
	req := &categoryPb.CategoryRequest{Id: id}
	_, err := s.client.DeleteCategory(ctx, req)
	if err != nil {
		return fmt.Errorf("failed to delete category: %w", err)
	}
	return nil
}
