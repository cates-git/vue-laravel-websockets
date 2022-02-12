export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Target: https://go.nuxtjs.dev/config-target
  target: 'static',

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'Chats',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' },
    ],
    link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
    '@/assets/css/main.css'
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    '@nuxtjs/moment',

    ['@nuxtjs/laravel-echo', {
      broadcaster: 'pusher',
      key: process.env.VUE_APP_WEBSOCKETS_KEY,
      wsHost: process.env.VUE_APP_WEBSOCKETS_SERVER,
      wsPort: 6001,
      forceTLS: false,
      disableStats: true
    }]
  ],

  moment: {
    timezone: true
  },

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    // https://go.nuxtjs.dev/bootstrap
    'bootstrap-vue/nuxt',
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios',
    '@nuxtjs/auth-next'
  ],

  // Axios module configuration: https://go.nuxtjs.dev/config-axios
  axios: {
    // Workaround to avoid enforcing hard-coded localhost:3000: https://github.com/nuxt-community/axios-module/issues/308
    // baseURL: 'http://local.chats.api/api',
    baseURL: process.env.NODE_ENV === 'development' ? process.env.BASE_URL : 'https://cates-chat-api.herokuapp.com/api'
  },

  router: {
    middleware: ['auth']
  },

  auth: {
    redirect: {
      home: "/",
      login: "/login",
      logout: "/login"
    },
    strategies: {
      local: {
        token: {
          property: "token",
          maxAge: 1800,
          global: true,
          type: "Bearer"
        },
        endpoints: {
          login: { url: "/login", method: "post" },
          refresh: { url: '/refresh', method: 'post' },
          logout: { url: "/logout", method: "post" },
          user: { url: "/profile", method: "get" }
        },
        user: {
          property: "data",
          autoFetch: true
        },
        tokenRequired: true
      }
    }
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},

  generate: {
    dir: '../prod-temp'
  }
}
