const path = require('path');
const isProd = process.env.NODE_ENV === 'production';

module.exports = {
  // Compile entry.scss
  styles: [
    {
      // Compile /components
      entry: path.resolve(__dirname, 'sass/**/*.scss'),
      dest: path.resolve(__dirname, 'css'),
      options: {
        sourceMap: isProd ? 'file' : true,
      },
    }],
};