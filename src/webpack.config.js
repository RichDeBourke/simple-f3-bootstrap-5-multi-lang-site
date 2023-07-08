const path = require('path');

module.exports = {
  entry: './src/js/main.js',
  output: {
    filename: 'main.min.js',
    path: path.resolve(__dirname, 'js')
  },
  devtool: 'source-map'
};
