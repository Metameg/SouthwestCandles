const path = require('path');
const webpack = require('webpack');

module.exports = (env) => {
	const isProduction = env.production === true || env.production === "true";
	console.log("MODE: " + isProduction);
	console.log("outfile: " + (isProduction ? 'bundle-prod.js' : 'bundle.js'));
  	return {
    	entry: './src/scripts/index.js',  // Entry file (your main JS file)
		output: {
			path: isProduction ? path.resolve(__dirname, 'dist', 'prod') : path.resolve(__dirname, 'dist', 'dev'), // Output directory
			filename: isProduction ? 'bundle-prod.js' : 'bundle.js', // Use bundle-prod.js for production
		  },
    	mode: isProduction ? 'production' : 'development',
		plugins: [
			new webpack.DefinePlugin({
				'process.env.BASE_URL': JSON.stringify(
					isProduction ? 'https://southwestcandles.shop' : 'http://localhost:3000'
				)
				
			})
		],
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
}