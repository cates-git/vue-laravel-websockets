<template>
  <b-container style="height: 100vh" class="d-flex flex-column justify-content-center">
    <form @submit.prevent="login" class="px-4">
      <!-- <b-img src="https://picsum.photos/500/500/?image=54" fluid rounded="circle" class="d-block mx-auto w-50 mb-5"></b-img> -->
      <h3>Chats</h3>
      <div class="form-group">
        <small class="form-text text-danger text-center mb-1" v-if="error">{{error}}</small>
        <input v-model="fields.email" type="email" placeholder="Email" class="form-control">

      </div>

      <div class="form-group">
        <input v-model="fields.password" type="password" placeholder="Password" class="form-control">
      </div>

      <b-button type="submit" block class="text-uppercase" variant="dark" :disabled="!(fields.email && fields.password)">Sign In</b-button>
      <!-- <div class="d-flex flex-row justify-content-between mt-2">
        <b-button @click="$router.push('/register')" type="button" variant="light" class="text-primary text-left">Sign Up</b-button>
        <b-button @click="$router.push('/reset')" type="button" variant="light" class="text-right">Forgot password?</b-button>
      </div> -->

        <b-button @click="$router.push('/register')" block type="button" variant="light" class="text-primary">Sign Up</b-button>
    </form>
  </b-container>
</template>

<script>
export default {
  layout: 'login',
  name: 'login',
  data () {
    return {
      fields: {
        email: '',
        password: ''
      },
      error: null
    }
  },
  methods: {
    async login () {
      try {
        await this.$auth.loginWith('local', { data: this.fields })
          .then((r) => {
            this.error = r.data.status ? null : r.data.message
          })
      } catch (e) {
        console.log(e)
        this.error = "Under maintenance. Please try again later."
      }
    }
  }
}
</script>
