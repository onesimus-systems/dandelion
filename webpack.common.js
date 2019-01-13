const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
  entry: {
    addlog: './public/src/ts/addlog.ts',
    admin: './public/src/ts/admin.ts',
    dashboard: './public/src/ts/dashboard.ts',
    editlog: './public/src/ts/editlog.ts',
    groupmanager: './public/src/ts/groupmanager.ts',
    login: './public/src/ts/login.ts',
    settings: './public/src/ts/settings.ts',
    singlelog: './public/src/ts/singlelog.ts',
    usermanager: './public/src/ts/usermanager.ts'
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
      }
    ]
  },

  resolve: {
    extensions: [ '.tsx', '.ts', '.js', '.elm' ]
  },
};
