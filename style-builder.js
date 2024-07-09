const fs = require('fs');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
//変数 devMode は production モードの場合は false でその他の場合は true
const devMode = process.env.NODE_ENV !== 'production';

const getScssEntries = (dir) => {
  return fs.readdirSync(dir)
    .filter(file => file.trim() !== '' && path.extname(file) !== '' && !file.startsWith('_'))
    .map(file => [file.replace(/\.[^/.]+$/, ''), `${dir}/${file}`]);
}

const entries = [
  ...getScssEntries('./src/scss'),
];

console.log(entries); // デバッグ出力

// CSSディレクトリを空にする
const outputDir = path.resolve(__dirname, 'assets/css');
if (fs.existsSync(outputDir)) {
  fs.rmSync(outputDir, { recursive: true });
  fs.mkdirSync(outputDir);
}

module.exports = {
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
    new RemoveEmptyScriptsPlugin(),
    new CssMinimizerPlugin()
  ],
  entry: Object.fromEntries(entries),
  output: {
    path: outputDir
  },
  module: {
    rules: [
      {
        test: /\.scss$/, // Sassファイルに対するルール
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  require('autoprefixer')(),
                  require('css-declaration-sorter')({
                    order: 'smacss',
                  }),
                  require('postcss-sort-media-queries')({
                    sort: 'desktop-first',
                  }),
                ]
              }
            }
          },
					{
						loader: 'sass-loader',
						options: {
							sassOptions: {
								outputStyle: 'expanded',
							},
						},
					},
        ],
      }
    ]
  },
  //source-map タイプのソースマップを出力
  devtool: devMode ? 'source-map' : 'eval',
  watchOptions: {
    ignored: /node_modules/  //正規表現で指定
  },
};
