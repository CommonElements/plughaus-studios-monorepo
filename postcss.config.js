module.exports = {
  plugins: [
    require('autoprefixer')({
      overrideBrowserslist: [
        '> 1%',
        'last 2 versions',
        'Firefox ESR',
        'not IE 11',
        'not dead'
      ],
      grid: 'autoplace'
    })
  ]
};