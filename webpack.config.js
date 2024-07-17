const fs = require('fs');
const path = require('path');

const entries = fs.readdirSync('./src/ts')
  .filter(file => {
    // ファイル名が空でなく、拡張子があるもののみを対象とする
    return file !== 'modules' && file.trim() !== '' && path.extname(file) !== '';
  })
  .map(file => [file.replace(/\.[^/.]+$/, ''), `./src/ts/${file}`]);

console.log(entries); // デバッグ出力

const entry = Object.fromEntries(entries);

// CSSディレクトリを空にする
const outputDir = path.resolve(__dirname, 'assets/js');
if (fs.existsSync(outputDir)) {
  fs.rmSync(outputDir, { recursive: true });
  fs.mkdirSync(outputDir);
}

module.exports = {
  mode: "development",
  // メインとなるJavaScriptファイル（エントリーポイント）
  entry:entry,

  // ファイルの出力設定
  output: {
    //  出力ファイルのディレクトリ名
    path: outputDir,
    // 出力ファイル名
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        // 拡張子 .ts の場合
        test: /\.ts$/,
        // TypeScript をコンパイルする
        use: {
          loader: 'ts-loader',
          options: {
            transpileOnly: true,
            configFile: "webpack.tsconfig.json",
          },
        },
      },
    ],
  },
  resolve: {
    // 拡張子を配列で指定
    extensions: [".ts", ".tsx", ".js", ".json"],
    alias: {
      "@js/*": [
        "./src/js/*"
      ],
    },
  },
  target: ["web", "es5"],

};
