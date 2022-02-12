<template>
  <b-container style="height: 100vh" class="d-flex flex-column justify-content-center">
    <form @submit.prevent="login" class="px-4">
      <b-img src="https://picsum.photos/500/500/?image=54" fluid rounded="circle" class="d-block mx-auto w-50 mb-5"></b-img>
      <div class="form-group">
        <small class="form-text text-danger text-center mb-1" v-if="error">{{error}}</small>
        <input v-model="fields.email" type="email" placeholder="Email" class="form-control">
      </div>

      <b-button type="submit" block class="text-uppercase" variant="dark" :disabled="!(fields.email)">Reset</b-button>
      <b-button type="button" variant="light" block class="text-primary" @click="$router.push('/login')">Sign In</b-button>
    </form>
  </b-container>
</template>

<script>
export default {
  layout: 'login',
  name: 'reset',
  auth: false,
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (vm.$auth.loggedIn) {
        vm.$router.replace("/");
      }
    });
  },
  data () {
    return {
      fields: {
        name: '',
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
