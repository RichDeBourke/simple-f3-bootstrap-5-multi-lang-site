const path = require('path');
const TerserPlugin = require("terser-webpack-plugin");

module.exports = {
  optimization: {
      minimize: true,
      minimizer: [new TerserPlugin()],
  },
  entry: './src/js/main.js',
  output: {
    filename: 'main.min.js',
    path: path.resolve(__dirname, 'js')
  },
  devtool: 'source-map',
  externals: {
    bootstrap: {
      modal: 'Modal',
      offcanvas: 'Offcanvas'
    }
  }
};
