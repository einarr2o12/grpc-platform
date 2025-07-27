#!/bin/bash

# Generate PHP gRPC client code from proto files

PROTO_DIR="../proto"
OUTPUT_DIR="src/Generated"

# Generate Category service client
protoc --proto_path=$PROTO_DIR \
    --php_out=$OUTPUT_DIR \
    --grpc_out=$OUTPUT_DIR \
    --plugin=protoc-gen-grpc=/opt/homebrew/bin/grpc_php_plugin \
    $PROTO_DIR/category.proto

# Generate Product service client
protoc --proto_path=$PROTO_DIR \
    --php_out=$OUTPUT_DIR \
    --grpc_out=$OUTPUT_DIR \
    --plugin=protoc-gen-grpc=/opt/homebrew/bin/grpc_php_plugin \
    $PROTO_DIR/product.proto

echo "gRPC PHP client code generated successfully!"