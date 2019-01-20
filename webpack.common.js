const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const debugMode = process.env.NODE_ENV !== 'production';

module.exports = {
  entry: {
    addlog: ['./public/src/ts/addlog.ts', './public/src/styles/addlog.scss'],
    admin: ['./public/src/ts/admin.ts', './public/src/styles/admin.scss'],
    dashboard: './public/src/ts/dashboard.ts', // Styles are imported from script
    editgroup: './public/src/styles/editgroup.scss',
    editlog: ['./public/src/ts/editlog.ts', './public/src/styles/editlog.scss'],
    edituser: './public/src/styles/edituser.scss',
    groupmanager: ['./public/src/ts/groupmanager.ts'],
    installer: './public/src/styles/installer.scss',
    login: ['./public/src/ts/login.ts', './public/src/styles/login.scss'],
    main: './public/src/styles/main.scss',
    settings: ['./public/src/ts/settings.ts', './public/src/styles/settings.scss'],
    singlelog: ['./public/src/ts/singlelog.ts', './public/src/styles/singlelog.scss'],
    usermanager: ['./public/src/ts/usermanager.ts'],
  },

  plugins: [
    new CleanWebpackPlugin([
      'public/dist/js',
      'public/dist/styles'
    ]),
    new MiniCssExtractPlugin({
      filename: debugMode ? '../styles/[name].css' : '../styles/[name].min.css',
      chunkFilename: debugMode ? '../styles/[id].css' : '../styles/[id].min.css'
    })
  ],

  output: {
    filename: debugMode ? '[name].js' : '[name].min.js',
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
        test: /\.scss$/,
        use: [{
          loader: MiniCssExtractPlugin.loader
        }, {
          loader: 'css-loader', options: {
            sourceMap: true
          }
        }, {
          loader: 'sass-loader', options: {
            sourceMap: true
          }
        }]
      },
      {
        test: /\.elm$/,
        exclude: [/elm-stuff/, /node-modules/],
        use: [{
          loader: 'elm-webpack-loader',
          options: {
            optimize: !debugMode,
            verbose: debugMode,
            debug: debugMode
          }
        }]
      }
    ]
  },

  resolve: {
    extensions: ['.tsx', '.ts', '.js', '.elm']
  }
};
