#!/bin/bash

# Generate Go gRPC client code from proto files

PROTO_DIR="../proto"
OUTPUT_DIR="internal/generated"

echo "Generating Go gRPC client code..."

# Generate Category service client
protoc --proto_path=$PROTO_DIR \
    --go_out=$OUTPUT_DIR/category \
    --go_opt=paths=source_relative \
    --go-grpc_out=$OUTPUT_DIR/category \
    --go-grpc_opt=paths=source_relative \
    $PROTO_DIR/category.proto

if [ $? -eq 0 ]; then
    echo "✅ Category service client generated"
else
    echo "❌ Failed to generate Category service client"
    exit 1
fi

# Generate Product service client  
protoc --proto_path=$PROTO_DIR \
    --go_out=$OUTPUT_DIR/product \
    --go_opt=paths=source_relative \
    --go-grpc_out=$OUTPUT_DIR/product \
    --go-grpc_opt=paths=source_relative \
    $PROTO_DIR/product.proto

if [ $? -eq 0 ]; then
    echo "✅ Product service client generated"
else
    echo "❌ Failed to generate Product service client"
    exit 1
fi

echo ""
echo "🎉 Go gRPC client code generated successfully!"
echo "Generated files are in: $OUTPUT_DIR"
echo ""
echo "Next steps:"
echo "1. Run: go mod tidy"
echo "2. Build: go build ./cmd/server"
echo "3. Test: curl http://localhost:8080/health"