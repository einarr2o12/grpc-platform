const mongoose = require('mongoose');

const connectDB = async () => {
  try {
    const conn = await mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/category-service', {
      useNewUrlParser: true,
      useUnifiedTopology: true,
    });
    console.log(`Category Service MongoDB Connected: ${conn.connection.host}`);
  } catch (error) {
    console.error('Category Service Database connection error:', error);
    process.exit(1);
  }
};

module.exports = connectDB;