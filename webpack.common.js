const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
  entry: {
    addlog: './public/source/ts/addlog.ts',
    admin: './public/source/ts/admin.ts',
    dashboard: './public/source/ts/dashboard.ts',
    editlog: './public/source/ts/editlog.ts',
    groupmanager: './public/source/ts/groupmanager.ts',
    login: './public/source/ts/login.ts',
    settings: './public/source/ts/settings.ts',
    singlelog: './public/source/ts/singlelog.ts',
    usermanager: './public/source/ts/usermanager.ts'
  },

  plugins: [
    new CleanWebpackPlugin(['public/dist/js'])
  ],

  output: {
    filename: '[name].min.js',
    path: path.resolve(__dirname, 'public/dist/js')
  },

  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: 'ts-loader',
        exclude: /node_modules/
      },
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
  },

  resolve: {
    extensions: [ '.tsx', '.ts', '.js', '.elm' ]//,
    // modules: [
    //   path.resolve(__dirname, "public/source/modules"),
    //   path.resolve(__dirname, "public/source/elm"),
    //   "node_modules"
    // ]
  },
};
