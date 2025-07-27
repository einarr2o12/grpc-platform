const grpc = require('@grpc/grpc-js');
const protoLoader = require('@grpc/proto-loader');
const path = require('path');
const connectDB = require('./models/database');
const Category = require('./models/Category');

const PROTO_PATH = path.join(__dirname, 'proto', 'category.proto');

const packageDefinition = protoLoader.loadSync(PROTO_PATH, {
  keepCase: true,
  longs: String,
  enums: String,
  defaults: true,
  oneofs: true
});

const categoryProto = grpc.loadPackageDefinition(packageDefinition).category;


async function getCategory(call, callback) {
  try {
    const categoryId = call.request.id;
    const category = await Category.findById(categoryId);
    
    if (category) {
      callback(null, {
        id: category._id.toString(),
        name: category.name,
        description: category.description
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Category not found'
      });
    }
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function listCategories(call, callback) {
  try {
    const categories = await Category.find({});
    const formattedCategories = categories.map(cat => ({
      id: cat._id.toString(),
      name: cat.name,
      description: cat.description
    }));
    callback(null, { categories: formattedCategories });
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function createCategory(call, callback) {
  try {
    const { name, description } = call.request;
    const category = new Category({ name, description });
    const savedCategory = await category.save();
    
    callback(null, {
      id: savedCategory._id.toString(),
      name: savedCategory.name,
      description: savedCategory.description
    });
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function updateCategory(call, callback) {
  try {
    const { id, name, description } = call.request;
    const category = await Category.findByIdAndUpdate(
      id,
      { name, description },
      { new: true }
    );
    
    if (category) {
      callback(null, {
        id: category._id.toString(),
        name: category.name,
        description: category.description
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Category not found'
      });
    }
  } catch (error) {
    callback({
      code: grpc.status.INTERNAL,
      details: error.message
    });
  }
}

async function deleteCategory(call, callback) {
  try {
    const categoryId = call.request.id;
    const category = await Category.findByIdAndDelete(categoryId);
    
    if (category) {
      callback(null, {
        success: true,
        message: 'Category deleted successfully'
      });
    } else {
      callback({
        code: grpc.status.NOT_FOUND,
        details: 'Category not found'
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
  grpcServer.addService(categoryProto.CategoryService.service, {
    getCategory: getCategory,
    listCategories: listCategories,
    createCategory: createCategory,
    updateCategory: updateCategory,
    deleteCategory: deleteCategory
  });
  
  const grpcPort = '50051';
  grpcServer.bindAsync(`0.0.0.0:${grpcPort}`, grpc.ServerCredentials.createInsecure(), (err, port) => {
    if (err) {
      console.error('Failed to bind gRPC server:', err);
      return;
    }
    console.log(`Category gRPC service running on port ${port}`);
    grpcServer.start();
  });

}

main();