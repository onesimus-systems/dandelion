const merge = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  devtool: 'inline-source-map',

  module: {
    rules: [
      {
        test: /\.elm$/,
        exclude: [/elm-stuff/, /node-modules/],
        use: [{
          loader: 'elm-webpack-loader',
          options: {
            verbose: true,
            debug: true
          }
        }]
      }
    ]
  }
});
