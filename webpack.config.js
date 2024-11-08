const path = require('path');

module.exports = {
  entry: './src/scripts/index.js',  // Entry file (your main JS file)
  output: {
    path: path.resolve(__dirname, 'dist'),  // Output directory
    filename: 'bundle.js',  // Output bundle file
  },
  mode: 'development',  // or 'production' depending on your needs
  module: {
    rules: [
      {
        test: /\.js$/,  // Apply Babel to JavaScript files
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],  // Allows modern JS syntax
          },
        },
      },
    ],
  },
  watch: true,  // Enable watch mode
};