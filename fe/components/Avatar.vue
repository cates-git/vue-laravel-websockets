<template>
  <div class="position-relative" :style="`width: ${width}; height: ${height}`">
    <b-img v-bind="{ blank: true, blankColor: '#777', class: 'd-inline-block w-100 h-100' }" rounded="circle" alt="Circle image"></b-img>
    <span class="h6 font-weight-light position-absolute m-0"
      style="left: 50%; top: 50%; transform: translate(-50%, -50%); color: #fff">
      {{ name.charAt(0).toUpperCase() }}
    </span>
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: 'A'
    },
    width: {
      type: String,
      default: '35px'
    },
    height: {
      type: String,
      default: '35px'
    }
  }
}
</script>
