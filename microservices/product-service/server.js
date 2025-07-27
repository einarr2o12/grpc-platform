const grpc = require('@grpc/grpc-js');
const protoLoader = require('@grpc/proto-loader');
const path = require('path');
const connectDB = require('./models/database');
const Product = require('./models/Product');

const PRODUCT_PROTO_PATH = path.join(__dirname, 'proto', 'product.proto');
const CATEGORY_PROTO_PATH = path.join(__dirname, 'proto', 'category.proto');

// Load product proto
const productPackageDefinition = protoLoader.loadSync(PRODUCT_PROTO_PATH, {
  keepCase: true,
  longs: String,
  enums: String,
  defaults: true,
  oneofs: true
});

// Load category proto for client
const categoryPackageDefinition = protoLoader.loadSync(CATEGORY_PROTO_PATH, {
  keepCase: true,
  longs: String,
  enums: String,
  defaults: true,
  oneofs: true
});

const productProto = grpc.loadPackageDefinition(productPackageDefinition).product;
const categoryProto = grpc.loadPackageDefinition(categoryPackageDefinition).category;

// Category service client
const categoryServiceUrl = process.env.CATEGORY_SERVICE_URL || 'localhost:50051';
const categoryClient = new categoryProto.CategoryService(categoryServiceUrl, grpc.credentials.createInsecure());


async function getProduct(call, callback) {
  try {
    const productId = call.request.id;
    const product = await Product.findById(productId);
    
    if (product) {
      callback(null, {
        id: product._id.toString(),
        name: product.name,
        description: product.description,
        price: product.price,
        category_id: product.category_id
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Product not found'
      });
    }
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function listProducts(call, callback) {
  try {
    const products = await Product.find({});
    const formattedProducts = products.map(prod => ({
      id: prod._id.toString(),
      name: prod.name,
      description: prod.description,
      price: prod.price,
      category_id: prod.category_id
    }));
    callback(null, { products: formattedProducts });
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function getProductsByCategory(call, callback) {
  try {
    const categoryId = call.request.category_id;
    const products = await Product.find({ category_id: categoryId });
    const formattedProducts = products.map(prod => ({
      id: prod._id.toString(),
      name: prod.name,
      description: prod.description,
      price: prod.price,
      category_id: prod.category_id
    }));
    callback(null, { products: formattedProducts });
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function createProduct(call, callback) {
  try {
    const { name, description, price, category_id } = call.request;
    const product = new Product({ name, description, price, category_id });
    const savedProduct = await product.save();
    
    callback(null, {
      id: savedProduct._id.toString(),
      name: savedProduct.name,
      description: savedProduct.description,
      price: savedProduct.price,
      category_id: savedProduct.category_id
    });
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function updateProduct(call, callback) {
  try {
    const { id, name, description, price, category_id } = call.request;
    const product = await Product.findByIdAndUpdate(
      id,
      { name, description, price, category_id },
      { new: true }
    );
    
    if (product) {
      callback(null, {
        id: product._id.toString(),
        name: product.name,
        description: product.description,
        price: product.price,
        category_id: product.category_id
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Product not found'
      });
    }
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function deleteProduct(call, callback) {
  try {
    const productId = call.request.id;
    const product = await Product.findByIdAndDelete(productId);
    
    if (product) {
      callback(null, {
        success: true,
        message: 'Product deleted successfully'
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Product not found'
      });
    }
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function main() {
  // Connect to MongoDB
  await connectDB();
  
  // gRPC Server
  const grpcServer = new grpc.Server();
  grpcServer.addService(productProto.ProductService.service, {
    getProduct: getProduct,
    listProducts: listProducts,
    getProductsByCategory: getProductsByCategory,
    createProduct: createProduct,
    updateProduct: updateProduct,
    deleteProduct: deleteProduct
  });
  
  const grpcPort = '50052';
  grpcServer.bindAsync(`0.0.0.0:${grpcPort}`, grpc.ServerCredentials.createInsecure(), (err, port) => {
    if (err) {
      console.error('Failed to bind gRPC server:', err);
      return;
    }
    console.log(`Product gRPC service running on port ${port}`);
    grpcServer.start();
  });

}

main();