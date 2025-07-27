const mongoose = require('mongoose');

const connectDB = async () => {
  try {
    const conn = await mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/product-service', {
      useNewUrlParser: true,
      useUnifiedTopology: true,
    });
    console.log(`Product Service MongoDB Connected: ${conn.connection.host}`);
  } catch (error) {
    console.error('Product Service Database connection error:', error);
    process.exit(1);
  }
};

module.exports = connectDB;