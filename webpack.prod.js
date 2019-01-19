const webpack = require('webpack');
const merge = require('webpack-merge');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  devtool: 'source-map',

  optimization: {
    minimizer: [new UglifyJSPlugin({
      sourceMap: true,
      uglifyOptions: {
        compress: {
          pure_funcs: "F2,F3,F4,F5,F6,F7,F8,F9,A2,A3,A4,A5,A6,A7,A8,A9",
          pure_getters: true,
          keep_fargs: false,
          unsafe_comps: true,
          unsafe: true
        }
      }
    })]
  },

  plugins: [
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify('production')
    })
  ],

  module: {
    rules: [
      {
        test: /\.elm$/,
        exclude: [/elm-stuff/, /node-modules/],
        use: [{
          loader: 'elm-webpack-loader',
          options: {
            optimize: true
          }
        }]
      }
    ]
  }
});
