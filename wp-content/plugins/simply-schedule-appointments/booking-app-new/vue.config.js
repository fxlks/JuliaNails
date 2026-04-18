// vue.config.js

module.exports = {
  filenameHashing: false,
  transpileDependencies: ['vue-tel-input'], // Ensures that vue-tel-input is transpiled by Babel to support modern JS syntax (e.g., ??, ?.)
  chainWebpack: config => {
    config
      .plugin('html')
      .tap(args => {
        args[0].title = 'Book an appointment'
        return args
      })
  },
  configureWebpack: {
    output: {
      jsonpFunction: 'jsonpFunction'
    }
  },
  devServer: {
    proxy: {
      '/wp-': {
          target: 'http://ssa.localdev',
          changeOrigin: true
      },
      '/getApi.php': {
          target: 'http://ssa.localdev/wp-content/plugins/simply-schedule-appointments',
          changeOrigin: true
      }
    }
  },

  assetsDir: 'static'
}